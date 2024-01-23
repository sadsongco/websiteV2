<?php

require_once(__DIR__.'/includes/api-header.php');

try {
    $query = "SELECT article_id,
        article_content,
        DATE_FORMAT(live_date, '%a %D %b, %Y') AS live_date
    FROM Articles 
    WHERE card_id = ?
    AND live_date < NOW()
    LIMIT 1;
    ";
    $stmt = $db->prepare($query);
    $stmt->execute(['about']);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
catch (PDOException $e) {
    exit ("Database error: ".$e->getMessage());
}

if (sizeof($result)==0) exit("<h1>ABOUT</h1>");

$article = $result[0];
$article['article_content'] = formatArticle($article['article_content']);
$article['article_content'] = preg_replace_callback('/<!--{{img-([0-9])+}}-->/',
    fn ($matches) => $m->render('article-image', getImageData($article['article_id'], $matches[0], $db)),
    $article['article_content']);

echo $m->render('card-content-single', $article);