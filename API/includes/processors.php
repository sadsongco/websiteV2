<?php

function p_2($input) {
    echo "<pre>"; print_r($input); echo "</pre>";
}

function formatArticle($content) {
    $input_arr = explode("\n", $content);
    $output = "";
    $nl_flag = false;
    foreach ($input_arr as $key=>$line) {
        $line = trim($line);
        if ($line != "" && $nl_flag) {
            $output .= "<br class='big'>$line";
            $nl_flag = true;
            continue;
        }
        $output .= $line;
        $nl_flag = true;
    }
    return $output;
}

function getImageData($db, $img_id, $img_align=null) {
    try {
        $query = "SELECT * FROM ArticleImages WHERE img_id = ?;";
        $stmt = $db->prepare($query);
        $stmt->execute([$img_id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (PDOException $e) {
        throw new Exception($e);
    }
    $html_align = null;
    switch ($img_align) {
        case "l":
            $html_align = "left";
            break;
        case "r":
            $html_align = "right";
            break;
    }
    $result[0]['align'] = $html_align;
    return $result[0];
}