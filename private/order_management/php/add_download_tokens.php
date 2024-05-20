<?php

require_once("../../../../secure/scripts/teo_order_connect.php");
include_once(__DIR__.'/../../mailout/api/includes/mailout_create.php');

// Templating
require '../lib/mustache.php-main/src/Mustache/Autoloader.php';
Mustache_Autoloader::register();

$m = new Mustache_Engine(array(
    'loader' => new Mustache_Loader_FilesystemLoader(__DIR__.'/../templates'),
    'partials_loader' => new Mustache_Loader_FilesystemLoader(__DIR__.'/../templates/partials')
));


//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require __DIR__.'/../../mailout/api/vendor/autoload.php';


function getHost() {
    $protocol = 'http';
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') $protocol .= 's';
    return "$protocol://".$_SERVER['HTTP_HOST'];
}


function makeUniqueToken($token, $email) {
    return hash('sha1', $token.$email);
}

function sendDownloadMail($email, $m) {
    echo "Send download mail<br>";
    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);
    require_once("../../../../secure/mailauth/teo.php");

    try {
        $subject_id = "[THE EXACT OPPOSITE]";
        $heading = "Download code for Skill Issue album";
        // set up emails
        $content_path = __DIR__."/../assets/download_mail_body/download_mail.txt";
        $host = getHost();
        $download_link = "$host/users/si_download.php?email=$email";
        $download_html = '<a href="'.$download_link.'">Download Now</a>';
        try {
            $content = file($content_path);
        }
        catch (Exception $e) {
            exit("FATAL: missing email body file: ".$e->getMessage()." - messages stopped");
        }

        $subject = $subject_id.array_shift($content);
        $heading = array_shift($content);
        $text_template = createTextBody($content);
        $html_template = createHTMLBody($content);
        $text_body = $m->render("textEmail", ["heading"=>$heading, "content"=>$text_template, "download_link"=>$download_link, "host"=>$host]);
        $html_body = $m->render("htmlEmail", ["heading"=>$heading, "content"=>$html_template, "download_link"=>$download_html, "host"=>$host]);

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
        $mail->addAddress($email);     //Add a recipient


        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $subject;
        $mail->msgHTML($html_body);
        $mail->AltBody = $text_body;

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        throw (new Exception($mail->ErrorInfo));
    }
}

try {
    $query = "SELECT Orders.customer_id, `email` FROM `Orders`
    LEFT JOIN `Order_items` ON Orders.order_id = Order_items.order_id
    LEFT JOIN `Customers` ON Orders.customer_id = Customers.customer_id
    WHERE Order_items.item_id = 2
    AND Orders.sumup_id < 999;";
    $result = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    $result = [
        ["customer_id"=>"90", "email"=>"nigel@thesadsongco.com"]
    ];
}

catch (PDOException $e) {
    exit("Database error: ".$e->getMessage());
}

$error = false;

foreach($result as $customer) {
    $token = makeUniqueToken($customer['customer_id'], $customer['email']);
        try {
            $query = "INSERT INTO download_tokens VALUES (NULL, ?, ?)";
            $stmt = $db->prepare($query);
            $stmt->execute([$customer['customer_id'], $token]);
            echo "Download token added for ".$customer['customer_id']."<br>";
            sendDownloadMail($customer['email'], $m);
            echo "email sent to ".$customer['email']."<br>";
            ob_flush();
            sleep(5);
        }
        catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                echo("Customer id ".$customer['customer_id']." already has token added<br>");
                $error = true;
            } else {
                exit("Database insert error: ".$e->getMessage());
            }
    }
}

echo $error ? "Database errors as above" : "Tokens added, emails sent";