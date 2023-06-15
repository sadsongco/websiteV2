<?php

require_once("../../../secure/scripts/teo_a_connect.php");

function resizeImage($file, $type, $max_width) {
    switch ($type) {
        case 'jpg':
        case 'jpeg':
            $image = imagecreatefromjpeg($file);
            break;
        case 'png':
            $image = imagecreatefrompng($file);
            break;
        case 'gif':
            $image = imagecreatefromgif($file);
            break;
        default:
            return null;
    }
    $img_resized = imagescale($image, $max_width, -1);
    // save resized image
    try {
        switch ($type) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($img_resized, $file);
                break;
            case 'png':
                imagepng($img_resized, $file);
                break;
            case 'gif':
                imagegif($img_resized, $file);
                break;
            default:
                return null;
        }
        return true;
    }
    catch (Exception $e) {
        error_log($e->getMessage() . "\n\r" . $e->getFile() . ":" / $e->getLine() . "\r\n");
        return false;
    }
}

function registerArticleImage($name, $id, $image_pos, $db) {
    // check it's not a duplicate entry
    $query = "SELECT * FROM ArticleImages
    WHERE article_id = ?
    AND img_pos = ?
    AND url = ?;";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute([$id, $image_pos, $name]);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($res) > 0) return true;
    }
    catch (Exception $e) {
        error_log($e->getMessage() . "\n\r" . $e->getFile() . ":" / $e->getLine() . "\r\n");
        return false;
    }
    // no duplicate, insert into db
    $query = "INSERT INTO ArticleImages
    VALUES (0, ?, ?, ?, '')";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute([$id, $image_pos, $name]);
        return true;
    }
    catch (Exception $e) {
        error_log($e->getMessage() . "\n\r" . $e->getFile() . ":" . $e->getLine() . "\r\n");
        return false;
    }
}

// submit article content
$parameters = [];
foreach ($_POST as $key=>$value) {
    switch ($key) {
        case 'article_content':
        case 'live_date':
        case 'card_id':
            $parameters[$key] = $value;
            break;
        default:
            break;
    }
}
if ($parameters['live_date'] == '') $parameters['live_date'] = date('Y-m-d H:i:s');
else $parameters['live_date'] = str_replace('T', ' ', $parameters['live_date']).":00";

$query = "INSERT INTO Articles VALUES (0, :article_content, NOW(), :live_date, :card_id);";

$result = [];
try {
    $stmt = $db->prepare($query);
    $stmt->execute($parameters);
    $result['success'] = true;
}
catch (PDOException $e) {
    $result['success'] = false;
    $result['error'] =  "Database Error: " . $e->getMessage() . "\n\r" . $e->getFile() . ":" . $e->getLine() . "\r\n";
}

// save any images

if (count($_FILES) > 0) {
    $max_image_width = 900;
    $result['image_upload'] = [];
    $article_id = $db->lastInsertId();
    $target_dir = '../../assets/images/article_images/';
    $image_pos = 0;
    foreach($_FILES as $file) {
        $file['name'] = str_replace(' ', '_', $file['name']);
        $target_file = $target_dir . $file['name'];
        $image_file_type = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        $image_size = getimagesize($file['tmp_name']);
        if ($image_size[0] > $max_image_width) resizeImage($file['tmp_name'], $image_file_type, $max_image_width);
        if (file_exists($target_file)) {
            array_push($result['image_upload'], $file['name']." exists");
            registerArticleImage($file['name'], $article_id, $image_pos, $db);
            $image_pos++;
            continue;
        };
        try {
            move_uploaded_file($file['tmp_name'], $target_file);
            array_push($result['image_upload'], $file['name']." success");
            registerArticleImage($file['name'], $article_id, $image_pos, $db);
            $image_pos++;
        }
        catch (Exception $e) {
            array_push($result['image_upload'], $file['name']." failed");
        }
    }

}

echo json_encode($result);

require_once("../../../secure/scripts/teo_disconnect.php");

?>