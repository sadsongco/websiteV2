<?php

// cd /home/thesadso/theexactopposite.uk/private/mailout/API/; /usr/local/bin/php -q mailout.php

function write_to_log ($fp, $ouput) {
    fwrite($fp, $output);
    fclose($fp);
}

function get_email_addresses($db, $mailout_id, $fp) {
    try {
        if ($mailout_id == 'test') {
            $mailout_id = 1;
            $mailing_table = "test_mailing_list";
        }
        else {
            $mailout_id = (int)$mailout_id;
            $mailing_table = "mailing_list";
        };
        $query = "SELECT email, name, email_id
        FROM $mailing_table
        WHERE last_sent < ?
        AND subscribed = 1
        AND error = 0
        ORDER BY domain
        LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->execute([$mailout_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    catch(PDOException $e) {
        write_to_log($fp, "\nget_email_addresses Database Error: " . $e->getMessage());
        exit();
    }
}

function replace_tags($body_template, $row) {
    $secure_id = hash('ripemd128', $row['email'].$row['email_id'].'JamieAndNigel');
    $row['secure_id'] = $secure_id;
    foreach ($row as $tag_name=>$tag_content) {
        if ($tag_name == 'name' && $tag_content == '') $tag_content = 'Music Friend';
        $body_template = str_replace("<!--{{".$tag_name."}}-->", $tag_content, $body_template);
    }
    return $body_template;
}

function mark_as_sent($db, $current_mailout, $row) {
    // $current_mailout = 1;
    if ($current_mailout == 'test') {
        $stmt = $db->prepare("UPDATE test_mailing_list SET last_sent = ? WHERE email_id = ? AND email = ?");
        $stmt->execute([1, $row['email_id'], $row['email']]);
        return "\n--TEST-- :: Message sent: ".htmlspecialchars($row['email']);}
    try {
        $stmt = $db->prepare("UPDATE mailing_list SET last_sent = ? WHERE email_id = ? AND email = ?");
        $stmt->execute([$current_mailout, $row['email_id'], $row['email']]);
        return 'Message sent: '.htmlspecialchars($row['email']);
    }
    catch(PDOException $e) {
        return  "mark_as_sent Database Error: " . $e->getMessage();
    }
}

function mark_as_error($db, $row, $current_mailout) {
    if ($current_mailout == 'test') {
        $stmt = $db->prepare("UPDATE test_mailing_list SET error = 1 WHERE email_id = ? AND email = ?");
        $stmt->execute([$row['email_id'], $row['email']]);
        return "--TEST-- :: ERROR SENDING: ".$row['email'];}
    try {
        $stmt = $db->prepare("UPDATE mailing_list SET error = 1 WHERE email_id = ? AND email = ?");
        $stmt->execute([$row['email_id'], $row['email']]);
        return 'ERROR SENDING: '.$row['email'];
    }
    catch(PDOException $e) {
        return "mark_as_error Database Error: " . $e->getMessage();
    }

}

/**
 * This example shows how to send a message to a whole list of recipients efficiently.
 */

//Import the PHPMailer class into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once('../../../../secure/scripts/teo_a_connect.php');

error_reporting(E_ERROR | E_PARSE);

date_default_timezone_set('Etc/UTC');

require 'vendor/autoload.php';

// paths to email data
$html_email_path = "./mailout_bodies/html/";
$text_email_path = "./mailout_bodies/text/";
$subject_path = "./mailout_bodies/subject/";
// set the current email
$current_mailout = file_get_contents('./current_mailout.txt');
if ($current_mailout == '') exit();
// create log
$fp = fopen("./logs/mailout_log_".$current_mailout.".txt", 'a');

//Passing `true` enables PHPMailer exceptions
$mail = new PHPMailer(true);

try {
    $body_template = file_get_contents($html_email_path.$current_mailout.'.html') or die ("FATAL: missing email body file: html");
    $text_template = file_get_contents($text_email_path.$current_mailout.'.txt') or die ("FATAL: missing email body file: text");
    $subject = file_get_contents($subject_path.$current_mailout.'.txt') or die ("FATAL: missing email body file: subject");
}
catch (Exception $e) {
    write_to_log($fp, "FATAL: missing email body file: ".$e->getMessage());
    exit();
}

$mail->isSMTP();
$mail->Host = 'theexactopposite.uk';
$mail->SMTPAuth = true;
$mail->SMTPKeepAlive = true; //SMTP connection will not close after each email sent, reduces SMTP overhead
$mail->Port = 25;
$mail->Username = 'info@theexactopposite.uk';
$mail->Password = 'AudienceBuildingExercise#23';
$mail->setFrom('info@theexactopposite.uk', 'The Exact Opposite mailing list');
$mail->addReplyTo('info@theexactopposite.uk', 'The Exact Opposite mailing list');


// $mail->isSMTP();
// $mail->Host = 'sandbox.smtp.mailtrap.io';
// $mail->SMTPAuth = true;
// $mail->Port = 2525;
// $mail->Username = '2be25e29cd2991';
// $mail->Password = 'aa9d83d9080798';
// $mail->setFrom('info@thesadsongco.com', 'The Sad Song Co. mailing list');
// $mail->addReplyTo('info@thesadsongco.com', 'The Sad Song Co. mailing list');

$mail->Subject = $subject;

$result = get_email_addresses($db, $current_mailout, $fp);
if (sizeof($result) == 0) {
    write_to_log($fp, "\n\n--------COMPLETE--------");
    $fp = fopen('current_mailout.txt', 'w');
    fwrite($fp, '');
    fclose($fp);
    $mail->msgHTML("<h2>ALL EMAILS SENT. Check ./logs/mailout_log_".$current_mailout.".txt for details<h2>");
    $mail->addAddress('info@thesadsongco.com', 'Info');
    $mail->send();
    exit();
}

$output = "";
$remove_path = 'https://theexactopposite.uk/email_management/unsubscribe_dd.php?email=<!--{{email}}-->&check=<!--{{secure_id}}-->';
foreach ($result as $row) {
    try {
        $body = replace_tags($body_template, $row);
        $mail->msgHTML($body);
        $text_body = replace_tags($text_template, $row);
        $mail->AltBody = $text_body;
        $mail->addAddress($row['email'], $row['name']);
    } catch (Exception $e) {
        $output .= "\n".mark_as_error($db, $row, $current_mailout);
        $output .=  "\nInvalid address ".$row['email']." skipped";
        $output .= "\nREMOVE: " . replace_tags($remove_path, $row);
        continue;
    }
    
    try {
        $mail->send();
        //Mark it as sent in the DB
        $output .=  "\n".mark_as_sent($db, $current_mailout, $row);
    } catch (Exception $e) {
        $output .= "\n".mark_as_error($db, $row, $current_mailout);
        $output .= "\nPHPMailer Error :: ".$mail->ErrorInfo;
        $output .= "\nREMOVE: " . replace_tags($remove_path, $row);
        //Reset the connection to abort sending this message
        //The loop will continue trying to send to the rest of the list
        $mail->getSMTPInstance()->reset();
    }
    //Clear all addresses and attachments for the next iteration
    $mail->clearAddresses();
    $mail->clearAttachments();
}

// create log
write_to_log($fp, $output);

include_once('../../../../secure/scripts/teo_disconnect.php');

?>