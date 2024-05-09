<?php

require_once("../../../../secure/scripts/teo_order_connect.php");
include_once("includes/p_2.php");
include_once(__DIR__.'/../../mailout/api/includes/replace_tags.php');


//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require __DIR__.'/../../mailout/api/vendor/autoload.php';


function makeUniqueToken($token, $email) {
    return hash('sha1', $token.$email);
}

function sendDownloadMail() {
    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);
    require_once("../../../../secure/mailauth/teo.php");

    try {

        $body_template = file_get_contents($bodies_path."html/".$last_mailout.".html");
        $text_template = file_get_contents($bodies_path."text/".$last_mailout.".txt");
        $subject = file_get_contents($bodies_path."subject/".$last_mailout.".txt");

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
        return $last_mailout;

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
        sendDownloadMail($customer['email']);
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

echo $error ? "Database errors as above" : "Tokens added";