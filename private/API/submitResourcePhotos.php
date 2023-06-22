<?php

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

$result = [];
$result['filesProcessed'] = [];

// paths
$resource_photo_path = "../../resources/resource_dirs/press_shots/";
$full_res_path = $resource_photo_path."full_res/";
$web_path = $resource_photo_path."web/";
$thumbnail_path = $resource_photo_path."thumbnail/";
// sizes
$web_width = 900;
$thumbnail_width = 200;

// open full_res path, read contents
if ($handle = opendir($full_res_path)) {
    while (false != ($file_name = readdir($handle))) {
        if (substr($file_name, 0, 1) == '.') continue;
        try {
            // make web version
            $image_file_type = strtolower(pathinfo($full_res_path.$file_name, PATHINFO_EXTENSION));
            copy($full_res_path.$file_name, $web_path.$file_name);
            resizeImage($web_path.$file_name, $image_file_type, $web_width);
            // make thumbnail
            copy($full_res_path.$file_name, $thumbnail_path.$file_name);
            resizeImage($thumbnail_path.$file_name, $image_file_type, $thumbnail_width);
            $result['success'] = true;
            array_push($result['filesProcessed'], $file_name);
        }
        catch (Exception $e) {
            $result['success'] = false;
            $result['error'] = $e->getMessage();
        }
    }
}

echo json_encode($result);

require_once("../../../secure/scripts/teo_disconnect.php");

?>