<?php

include_once('includes/api-header.php');

try {
    $query = "SELECT card_id FROM Cards";
    $result =  $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
}
catch (PDOException $e) {
    echo $e->getMessage();
}

echo $m->render('nav', ["cards"=>$result]);