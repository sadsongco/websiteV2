<?php

require_once('includes/mailout_includes.php');

// paths to email data
$content_path = "../assets/content/teo/";
$remove_path = '/email_management/unsubscribe.php';
$subject_id = "[THE EXACT OPPOSITE]";

if (isset($_GET['dd'])) {
    $content_path = "../assets/content/dd/";
    $remove_path = '/email_management/unsubscribe_dd.php';
    $subject_id = "[DIVE DIVE]";
    $current_mailout = file_get_contents('./dd_current_mailout.txt');
}


$current_mailout = $_GET['mailout'];

if ($current_mailout == '') exit("Select a mailout to preview...");

$content = file($content_path.$current_mailout.".txt");

$subject = $subject_id.array_shift($content);
$heading = array_shift($content);
$text_template = createTextBody($content);
$html_template = createHTMLBody($content);
$host = getHost();

$text_body = $m->render("textTemplate", ["heading"=>$heading, "content"=>$text_template, "host"=>$host, "remove_path"=>$remove_path, "name"=>"Preview Name", "email"=>"previewemail@preview.com", "secure_id"=>"abcd123456789"]);
$html_body = $m->render("htmlTemplate", ["heading"=>$heading, "content"=>$html_template, "host"=>$host, "remove_path"=>$remove_path, "name"=>"Preview Name", "email"=>"previewemail@preview.com", "secure_id"=>"abcd123456789"]);

echo $m->render('mailoutPreview', ["text_body"=>$text_body, "html_body"=>$html_body, "subject"=>$subject]);