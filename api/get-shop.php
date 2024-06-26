<?php

    require_once(__DIR__.'/includes/api-header.php');
    
    try {
        $query = "SELECT article_id,
            article_content,
            article_title,
            DATE_FORMAT(live_date, '%a %D %b, %Y') AS display_date
        FROM Articles 
        WHERE card_id = ?
        AND live_date < NOW()
        ORDER BY live_date DESC
        LIMIT 1;
        ";
        $stmt = $db->prepare($query);
        $stmt->execute(['\shop']);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (PDOException $e) {
        exit ("Database error: ".$e->getMessage());
    }
    
    if (sizeof($result)==0) exit("<h1>SHOP</h1><p>Follow this link (opens in new window): <a href=\"https://teo-shop.sumupstore.com/\" target=\"_blank\">TEO Online Shop</a>");
    
    $article = $result[0];
    $article['article_content'] = formatArticle($article['article_content']);
    $article['article_content'] = preg_replace_callback('/<!--{{i::([0-9]+)(::)?(l|r)?}}-->/',
    fn ($matches) => $m->render('article-image', getImageData($db, $matches[1], isset($matches[3]) ? $matches[3] : null)),
    $article['article_content']);

    echo $m->render('card-content-single', $article);