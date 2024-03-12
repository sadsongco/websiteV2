<?php

require_once('../../../../secure/scripts/teo_a_connect.php');

require '../../../lib/mustache.php-main/src/Mustache/Autoloader.php';
Mustache_Autoloader::register();

$m = new Mustache_Engine(array(
    'loader' => new Mustache_Loader_FilesystemLoader('../assets/templates'),
    'partials_loader' => new Mustache_Loader_FilesystemLoader('../assets/templates/partials')
));

include_once("../../../email_management/includes/get_host.php");

/* *** FUNCTIONS *** */
function replaceHTMLLink($line) {
    $links = [];
    preg_match_all('/{{link}}(.*){{\/link}}/', $line, $links);
    $replacements = [];
    foreach ($links[0] as $key=>$link) {
        $replacements[] = ["search"=>$links[0][$key], "replace"=>$links[1][$key]];
    }
    if (sizeof($replacements)==0) return $line;
    foreach ($replacements as $replace) {
        $html_replace = '<a href="'.$replace["replace"].'">'.$replace["replace"].'</a>';
        $line = str_replace($replace["search"], $html_replace, $line);
    }
    return $line;
}

function createHTMLBody($content) {
    $body = "<p>";
    for ($x = 0; $x < sizeof($content); $x++) {
        if ($content[$x] == "" || $content[$x] == "\n") continue;
        $content[$x] = replaceHTMLLink($content[$x]);
        if ($x+1 < sizeof($content) && ($content[$x+1] == "" || $content[$x+1] == "\n")) {
            $body .= trim($content[$x])."</p>\n<p>";
            continue;
        }
        $body .= trim($content[$x])."<br />\n";
    }
    $body .= "</p>";
    return $body;
}

function createTextBody($content) {
    foreach($content as &$line) {
        $line = preg_replace('/{{link}}(.*){{\/link}}/', '$1', $line);
    }
    return implode("", $content);
}

