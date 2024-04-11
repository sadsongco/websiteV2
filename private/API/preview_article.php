<?php

include_once('includes/private-api-header.php');

include_once('../../api/includes/processors.php');

$article = $_GET;

$article['article_content'] = formatArticle($article['article_content']);
$article['article_content'] = preg_replace_callback('/<!--{{i::([0-9]+)(::)?(l|r)?}}-->/',
fn ($matches) => $m->render('article-image', getImageData($db, $matches[1], isset($matches[3]) ? $matches[3] : null)),
$article['article_content']);

echo $m->render('articlePreview', $article);