<?php

function replace_tags($body_template, $row) {
    $row['secure_id'] = $row['check'];
    $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https'?'https':'http';
    $row['host'] = "$protocol://".$_SERVER['HTTP_HOST'];
    foreach ($row as $tag_name=>$tag_content) {
        if ($tag_name == 'name' && $tag_content == '') $tag_content = 'Music Friend';
        $body_template = str_replace("<!--{{".$tag_name."}}-->", $tag_content, $body_template);
    }
    return $body_template;
}

?>