<?php

include_once('includes/private-api-header.php');

function createNewScript($name) {
    try {


    $fp = fopen($name, 'x');
    fwrite($fp, '<?php

    require_once(__DIR__.\'/includes/api-header.php\');
    
    try {
        $query = "SELECT article_id,
            article_content,
            article_title,
            DATE_FORMAT(live_date, \'%a %D %b, %Y\') AS display_date
        FROM Articles 
        WHERE card_id = ?
        AND live_date < NOW()
        ORDER BY live_date DESC
        LIMIT 1;
        ";
        $stmt = $db->prepare($query);
        $stmt->execute([\'\shop\']);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (PDOException $e) {
        exit ("Database error: ".$e->getMessage());
    }
    
    if (sizeof($result)==0) exit("<h1>SHOP</h1>");
    
    $article = $result[0];
    $article[\'article_content\'] = formatArticle($article[\'article_content\']);
    $article[\'article_content\'] = preg_replace_callback(\'/<!--{{i::([0-9]+)(::)?(l|r)?}}-->/\',
    fn ($matches) => $m->render(\'article-image\', getImageData($db, $matches[1], isset($matches[3]) ? $matches[3] : null)),
    $article[\'article_content\']);
    
    echo $m->render(\'card-content-single\', $article);');
    fclose($fp);
}
catch (Exception $e) {
    throw new Exception("render script couldn't be created");
}
}


// p_2($_POST);

try {
    $_POST['card_id'] = strtolower($_POST['title']);
    $query = "INSERT INTO Cards Values (:card_id, :title, :strap, :content_type, NULL, NULL, :card_pos);";
    $stmt = $db->prepare($query);
    $stmt->execute($_POST);
}
catch (PDOException $e) {
    exit ("Couldn't create Card: ".$e->getMessage());
}

$error = null;
$script_name = "../../api/get-".$_POST['card_id'].".php";
if (!file_exists($script_name)) {
    try {
        createNewScript($script_name);
        $render_script_created = true;
    }
    catch (Exceotion $e) {
        $render_script_created = false;
        $error = $e->getMessage();
    }
}

header ("HX-Trigger: cardCreated");
echo $m->render('cardCreated', ['render_script_created'=>$render_script_created, 'script_name'=>$script_name, 'error'=>$error]);