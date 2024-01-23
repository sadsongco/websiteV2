<?php

require_once(__DIR__.'/includes/api-header.php');

try {// update this with nl2br in php
    $query = "SELECT article_id,
        article_title,
        article_content, 
        DATE_FORMAT(live_date, '%a %D %b, %Y') AS live_date
    FROM Articles 
    WHERE card_id = ?
    AND live_date < NOW()";
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
    $article['article_content'] = preg_replace_callback('/<!--{{img-([0-9])+}}-->/',
    fn ($matches) => $m->render('article-image', getImageData($article['article_id'], $matches[0], $db)),
    $article['article_content']);
    $output['articles'][] = $article;
}

echo $m->render('card-content-multi', $output);