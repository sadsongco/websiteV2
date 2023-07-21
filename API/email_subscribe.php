<?php

require_once("../../secure/scripts/teo_a_connect.php");

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require '../private/mailout/API/vendor/autoload.php';
include_once('../email_management/includes/replace_tags.php');

function sendConfirmationEmail($row) {
    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);
    include('../../secure/mailauth/teo.php');

    try {
        $body_template = file_get_contents('mail_bodies/confirm.html');
        $text_template = file_get_contents('mail_bodies/confirm.txt');
        $subject = 'The Exact Opposite - confirm your email';

        $body = replace_tags($body_template, $row);
        $text_body = replace_tags($text_template, $row);

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


        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = $text_body;

        $mail->send();
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
}

$output = "404 Not Found";

$post = file_get_contents('php://input');
$post = json_decode($post, true);

// $post['email'] = 'nigel@thesadsongco.com';
// $post['name'] = '';

if (isset($post['email']) && $post['email'] != '') {
    try {
        include ('../../secure/secure_id/secure_id.php');
        $stmt = $db->prepare("INSERT INTO mailing_list (email, domain, name, last_sent, subscribed, date_added) VALUES (?, SUBSTRING_INDEX(?, '@', -1), ?, ?, ?, NOW())");
        $stmt->execute([$post['email'], $post['email'], $post['name'], 0, 1]);
        $secure_id = generateSecureId($post['email'], $db->lastInsertID());
        sendConfirmationEmail(['email'=>$post['email'], 'name'=>$post['name'], 'check'=>$secure_id]);
        $output = ['success'=> true];
    }
    catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $output = ['success'=> false, 'status'=>'exists'];
        } else {
            $output = ['succes'=>false, 'status'=>'db_error'];
            error_log($e->getMessage());
        }
    }
}

echo json_encode($output);


require_once("../../secure/scripts/teo_disconnect.php");

?>