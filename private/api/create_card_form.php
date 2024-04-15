<?php

include_once('includes/private-api-header.php');

function getContentTypes ($db) {
    $query = "SELECT COLUMN_TYPE 
    FROM information_schema.`COLUMNS` 
    WHERE TABLE_NAME = 'Cards' 
         AND COLUMN_NAME = 'content_type';";
    $result = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    preg_match("/^enum\(\'(.*)\'\)$/", $result[0]['COLUMN_TYPE'], $matches);
    $enum = explode("','", $matches[1]);
    return $enum;
}

function getLastCardPos ($db) {
    $query = "SELECT MAX(card_pos) FROM `Cards`;";
    $result = $db->query($query)->fetch(PDO::FETCH_NUM);
    return $result[0];
    
}

$content_types = getContentTypes($db);
$card_pos = getLastCardPos($db) + 1;

echo $m->render('createCard', ["content_types"=>$content_types, "card_pos"=>$card_pos]);