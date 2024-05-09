<?php

include_once("./includes/html_head.php");

require_once("../../secure/scripts/teo_a_connect.php");

require '../lib/mustache.php-main/src/Mustache/Autoloader.php';
Mustache_Autoloader::register();

$m = new Mustache_Engine(array(
    'loader' => new Mustache_Loader_FilesystemLoader('../private/mailout/assets/templates'),
    'partials_loader' => new Mustache_Loader_FilesystemLoader('../private/mailout/assets/templates/partials')
));

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require '../private/mailout/api/vendor/autoload.php';
include_once("includes/get_host.php");
include_once('../private/mailout/api/includes/replace_tags.php');
include_once('../private/mailout/api/includes/mailout_create.php');

function getLatestMailout() {
    $latest_mailout = 0;
    if ($handle = opendir('../private/mailout/assets/content/teo')) {
        while (false !== ($entry = readdir($handle))) {
            if (substr($entry, 0, 1) != ".") {
                $mailout_id = explode('.', $entry)[0];
                if ($mailout_id == 'test') continue;
                if ((int)$mailout_id > $latest_mailout) $latest_mailout = (int)$mailout_id;
            }
        }
        closedir($handle);
    }
    return $latest_mailout;
}

function sendLastMailout($row, $m) {

    $current_mailout = getLatestMailout();
    if ($current_mailout == 0) return null;
    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);
    require_once("../../secure/mailauth/teo.php");


    try {

        // paths to email data
        $content_path = "../private/mailout/assets/content/teo/";
        $remove_path = '/email_management/unsubscribe.php';
        $subject_id = "[THE EXACT OPPOSITE]";
        $mailing_list_table = $current_mailout == "test" ? "test_mailing_list" : "mailing_list";
        $log_dir =  $current_mailout == "test" ? './logs/test/' : './logs/teo/';

        try {
            $content = file($content_path.$current_mailout.'.txt');
        }
        catch (Exception $e) {
            write_to_log($log_fp, "\nFATAL: missing email body file: ".$e->getMessage());
            delete_current_mailout($current_mailout_file);
            email_admin($mail, "FATAL: missing email body file: ".$e->getMessage()." - messages stopped");
            exit();
        }
        
        
        $mail->isSMTP();
        $mail->Host = $mail_auth['host'];
        $mail->SMTPAuth = true;
        $mail->SMTPKeepAlive = true; //SMTP connection will not close after each email sent, reduces SMTP overhead
        $mail->Port = 25;
        $mail->Username = $mail_auth['username'];
        $mail->Password = $mail_auth['password'];
        $mail->setFrom($mail_auth['from']['address'], $mail_auth['from']['name']);
        $mail->addReplyTo($mail_auth['reply']['address'], $mail_auth['reply']['name']);
        //Recipients
        $mail->addAddress($row['email'], $row['name']);     //Add a recipient
        
        $subject = $subject_id.array_shift($content);
        $heading = array_shift($content);
        $text_template = createTextBody($content);
        $html_template = createHTMLBody($content);
        $host = getHost();

        //Content
        $mail->Subject = $subject;
        $secure_id = generateSecureId($row['email'], $row['email_id']);
        $text_body = $m->render("textTemplate", ["heading"=>$heading, "content"=>$text_template, "host"=>$host, "remove_path"=>$remove_path, "name"=>$row['name'], "email"=>$row['email'], "secure_id"=>$secure_id]);
        $html_body = $m->render("htmlTemplate", ["heading"=>$heading, "content"=>$html_template, "host"=>$host, "remove_path"=>$remove_path, "name"=>$row['name'], "email"=>$row['email'], "secure_id"=>$secure_id]);
        $mail->msgHTML($html_body);
        $mail->AltBody = $text_body;

        $mail->send();
        return $current_mailout;

    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        throw (new Exception($mail->ErrorInfo));
    }
}

$message = 'There was an error. Make sure you only access this page from your email link';

if (isset($_GET) && isset($_GET['email'])) {
    try {
        include_once('../../secure/secure_id/secure_id.php');
        $stmt = $db->prepare('SELECT email_id, email, name FROM mailing_list WHERE email=?');
        $stmt->execute([$_GET['email']]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (sizeof($result) == 0) throw new PDOException("Email not found", 2300);
        $email_id = $result[0]['email_id'];
        $secure_id = generateSecureId($_GET['email'], $email_id);
        if ($_GET['check'] != $secure_id) throw new PDOException('Bad check code', 1176);
        $row = $result[0];
        $stmt = $db->prepare('UPDATE mailing_list SET confirmed = 1 WHERE email_id = ?');
        $stmt->execute([$email_id]);
        $last_mailout = sendLastMailout($row, $m);
        if ($last_mailout != null) {
            // update last sent
            $query = 'UPDATE mailing_list SET last_sent = ? WHERE email_id = ?;';
            $stmt = $db->prepare($query);
            $stmt->execute([$last_mailout, $email_id]);
        }
        $message = 'Your email is confirmed, welcome to the email list!';
    }
    catch (PDOException $e) {
        if ($e->getCode() ==1176) {
            $message = 'Bad check code';
        }
        if ($e->getCode() == 2300) {
            $message = "Email not found";
        }
        error_log($e->getMessage());
    }
}

require_once("../../secure/scripts/teo_disconnect.php");

echo $message;

include_once("./includes/html_foot.php");
?>
