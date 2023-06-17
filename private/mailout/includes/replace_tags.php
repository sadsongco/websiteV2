<?php

function replace_tags($body_template, $row) {
    $secure_id = hash('ripemd128', $row['email'].$row['email_id'].'JamieAndNigel');
    $row['secure_id'] = $secure_id;
    $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https'?'https':'http';
    $row['host'] = "$protocol://".$_SERVER['HTTP_HOST'];
    foreach ($row as $tag_name=>$tag_content) {
        $body_template = str_replace("<!--{{".$tag_name."}}-->", $tag_content, $body_template);
    }
    return $body_template;
}
?>