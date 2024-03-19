<?php

// include_once("includes/mailout_includes.php");

$content_dir = isset($_POST['dd']) ? "../assets/content/dd" : "../assets/content/teo";

$filename = $_POST['filename'].".txt";

$path = $content_dir."/".$filename;

$fp = fopen($path, "w");

$mailout_content = $_POST['subject']."\n".$_POST['heading']."\n".$_POST['content'];

if(fwrite($fp, $mailout_content)) {
    header("HX-Trigger: listChange");
    echo "mailout $filename saved";
} else {
    echo "mailout save failed";
}