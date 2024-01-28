<?php

include_once("includes/private-api-header.php");

try {
    $query = "SELECT
        article_id,
        article_title,
        article_content,
        card_id,
        DATE_FORMAT(post_date, '%D %b, %Y') AS post_date,
        DATE_FORMAT(live_date, '%D %b, %Y') AS live_date
    FROM Articles WHERE card_id = ? ORDER BY post_date DESC ";
    if ($_GET['content_type'] == 'single') {
        $query .= "LIMIT 1";
    }
    $stmt = $db->prepare($query);
    $stmt->execute([$_GET['card_id']]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
    echo "Database Error: " . $e->getMessage();
    exit();
}

$output['articles'] = $result;

echo $m->render('articleList', $output);