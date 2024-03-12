<?php

require_once('includes/mailout_includes.php');

$content_dir = isset($_GET['dd']) ? "../assets/content/dd" : "../assets/content/teo";
$mailoutOptions = [];

if ($handle = opendir($content_dir)) {
    while (false !== ($entry = readdir($handle))) {
        if (substr($entry, 0, 1) != ".")
        array_push($mailoutOptions, str_replace(".txt", "", $entry));
    }

    closedir($handle);
}

echo $m->render("selectMailoutOptions", ["options"=>$mailoutOptions]);