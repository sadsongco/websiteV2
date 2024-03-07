<?php

include_once('includes/private-api-header.php');

try {
    $query = "DELETE FROM Articles WHERE article_id=?;";
    $stmt = $db->prepare($query);
    $stmt->execute([$_POST['article_id']]);
    $message = "Article Deleted";
} catch (Exception $e) {
    $message = "Article couldn't be deleted: ".$e->getMessage();
}

header("HX-Trigger: articleDeleted");
echo $message;