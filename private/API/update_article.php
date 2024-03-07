<?php

include_once('includes/private-api-header.php');

try {
    $query = "UPDATE Articles SET
        article_title = :article_title,
        article_content = :article_content,
        post_date = :post_date,
        live_date = :live_date
        WHERE article_id = :article_id;";
    $stmt = $db->prepare($query);
    $stmt->execute($_POST);
}
catch (PDO_EXCEPTION $e) {
    die('Error updating article: '.$e->getMessage());
}

header("HX-Trigger: articleUpdated");
echo "<h2>Article Updated</h2>";