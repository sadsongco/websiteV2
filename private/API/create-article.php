<?php

include_once('includes/private-api-header.php');

if ($_POST['live_date'] == "") $_POST['live_date'] = date("Y-m-d H:i:s");
if ($_POST['article_content'] == "") exit($m->render('createdArticle', ["error"=>true, "msg"=>"No article content"]));
if ($_POST['card_id'] == "") exit($m->render('createdArticle', ["error"=>true, "msg"=>"No card id"]));

p_2($_POST);

try {
    $query = "INSERT INTO Articles VALUES (0, :article_title, :article_content, NOW(), :live_date, :card_id);";
    $stmt = $db->prepare($query);
    $stmt->execute($_POST);
}

catch (PDOException $e) {
    exit($m->render('createdArticle', ["error"=>true, "msg"=>"Database error: ".$e->getMessage()]));
}

echo $m->render('createdArticle');