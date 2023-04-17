<?php

$output = array(
    array(
        "target" => "other",
        "content" => "Lorem, ipsum dolor sit amet consectetur adipisicing elit. Laborum at vel ea quae aperiam a iusto facilis perspiciatis expedita velit, quam corrupti. Excepturi nam temporibus asperiores magni molestiae? Obcaecati, distinctio?"
    ),
    array(
        "target" => "about",
        "content" => "Lorem ipsum dolor sit amet consectetur adipisicing elit. Corrupti voluptatem soluta sequi enim omnis cupiditate. Voluptatum, quos aut libero a obcaecati expedita nam, facere adipisci eligendi debitis velit iusto tenetur?"
    )
);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS, post, get');
header('Access-Control-Max-Age', '3600');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
header('Access-Control-Allow-Credentials', 'true');

echo json_encode($output);

?>