<?php

require_once("../../secure/scripts/teo_connect.php");
require '../lib/mustache.php-main/src/Mustache/Autoloader.php';
Mustache_Autoloader::register();

$m = new Mustache_Engine(array(
    'loader' => new Mustache_Loader_FilesystemLoader('../templates'),
    'partials_loader' => new Mustache_Loader_FilesystemLoader('../templates/partials')
));

function p_2($input) {
    echo "<pre>"; print_r($input); echo "</pre>";
}

function formatArticle($content) {
    $input_arr = explode("\n", $content);
    $output = "";
    $nl_flag = false;
    foreach ($input_arr as $key=>$line) {
        $line = trim($line);
        if ($line != "" && $nl_flag) {
            $output .= "<br class='big'>$line";
            $nl_flag = true;
            continue;
        }
        $output .= $line;
        $nl_flag = true;
    }
    return $output;
}

function getImageData($article_id, $img_pos, $db) {
    try {
        $query = "SELECT * FROM ArticleImages WHERE article_id = ? AND img_pos = ?;";
        $stmt = $db->prepare($query);
        $stmt->execute([$article_id, $img_pos]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (PDOException $e) {
        throw new Exception($e);
    }
    return $result[0];
}