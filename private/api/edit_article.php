<?php

include_once('includes/private-api-header.php');

try {
    $query = "SELECT * FROM Articles WHERE article_id = ?;";
    $stmt = $db->prepare($query);
    $stmt->execute([$_GET['article_id']]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
catch (PDOException $e) {
    exit($e->getMessage());
}

$params = ['article_data'=>$result[0]];
header ("HX-Trigger-After-Settle: articleLoaded");

echo $m->render('editArticle', $params);