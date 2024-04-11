<?php

require_once(__DIR__.'/includes/api-header.php');

try {// update this with nl2br in php
    $query = "SELECT article_id,
        article_title,
        article_content,
        DATE_FORMAT(live_date, '%a %D %b, %Y') AS display_date
    FROM Articles 
    WHERE card_id = ?
    AND live_date < NOW()
    ORDER BY live_date DESC";
    $stmt = $db->prepare($query);
    $stmt->execute(['news']);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
catch (PDOException $e) {
    exit ("Database error: ".$e->getMessage());
}

if (sizeof($result)==0) exit("<h1>NEWS</h1>");

$output = [];
$output['articles'] = [];
foreach ($result as $article) {
    $article['article_content'] = formatArticle($article['article_content']);
    $article['article_content'] = preg_replace_callback('/<!--{{i::([0-9]+)(::)?(l|r)?}}-->/',
    fn ($matches) => $m->render('article-image', getImageData($db, $matches[1], isset($matches[3]) ? $matches[3] : null)),
    $article['article_content']);
    $output['articles'][] = $article;
}

echo $m->render('card-content-multi', $output);