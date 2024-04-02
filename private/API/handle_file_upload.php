<?php

include_once('includes/private-api-header.php');
include_once('includes/media_upload.php');

define("IMAGE_UPLOAD_PATH", __DIR__."/../../assets/images/article_images/");
define("MAX_IMAGE_WIDTH", 600);
define("IMAGE_THUMBNAIL_WIDTH", 100);
define("MAX_FILE_SIZE", return_bytes(ini_get("upload_max_filesize")));
define("MAX_POST_SIZE", return_bytes(ini_get("post_max_size")));

$uploaded_files = [];
if (!isset($_FILES) || !isset($_FILES["files"])) {
    $uploaded_files[] = ["success"=>false, "messsage"=>"No files uploaded"];
} else {
    $files = $_FILES["files"];
    $post_size = 0;
    foreach ($files["name"] as $key=>$filename) {
        if ($files["size"][$key] > MAX_FILE_SIZE || $files["tmp_name"][$key] == "") {
            $uploaded_files[] = ["success"=> false, "message"=>"File $filename is too big"];
            break;
        }
        $post_size += $files["size"][$key];
        if ($post_size > MAX_POST_SIZE) {
            $uploaded_files[] = ["success"=> false, "message"=>"File upload size too big - try doing them one at a time"];
            break;
        }        
        $image_file_type = strtolower(pathinfo($filename,PATHINFO_EXTENSION));
        switch ($image_file_type) {
            case "jpg":
            case "jpeg":
            case "png":
            case "gif":
                try {
                    $uploaded_files[] = uploadMedia($files, $key, $db, "ArticleImages", $image_file_type);
                }
                catch (Exception $e) {
                    $uploaded_files[] = ["success"=>false, "message"=>"System error: ".$e->getMessage()];
                }
                break;
            default:
            $uploaded_files[] = ["success"=>false, "message"=>$files["name"][$key].": $image_file_type file types are not supported"];
        }
    }
    
}

echo $m->render("uploadedFiles", ["uploaded_files"=>$uploaded_files]);
