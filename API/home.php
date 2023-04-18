<?php

$output = array(
    array(
        "target" => "other",
        "content" => "!!OTHER :::: Lorem, ipsum dolor sit amet consectetur adipisicing elit. Laborum at vel ea quae aperiam a iusto facilis perspiciatis expedita velit, quam corrupti. Excepturi nam temporibus asperiores magni molestiae? Obcaecati, distinctio?"
    ),
    array(
        "target" => "about",
        "content" => "!!!ABOUT :::: Lorem ipsum dolor sit amet consectetur adipisicing elit. Corrupti voluptatem soluta sequi enim omnis cupiditate. Voluptatum, quos aut libero a obcaecati expedita nam, facere adipisci eligendi debitis velit iusto tenetur?"
    ),
    array(
        "target" => "album",
        "content" => "ALBUM :::: Lorem ipsum dolor sit amet consectetur adipisicing elit. Officia dicta unde voluptatibus earum adipisci expedita non veritatis dolorem? Harum, earum voluptatibus? Maxime iure velit voluptates quisquam amet ea culpa, eos enim. Modi sapiente qui cumque assumenda sunt? Officiis nobis repellendus dicta neque similique voluptatum ab facilis eaque obcaecati ut temporibus in, asperiores eveniet nihil, corporis odio quae alias.\n\nQuia, facilis, sit expedita voluptatem illo, quasi eos est ad laudantium reiciendis ex! Delectus odit quae sapiente, aliquid asperiores laboriosam eveniet officiis, cupiditate, id deleniti earum itaque in eligendi maxime nobis reprehenderit doloribus! Vel aliquid non quo eum eius in consequatur consectetur error nemo perspiciatis repudiandae nesciunt.\n\n Magni voluptas dignissimos dolore quibusdam praesentium natus et ipsum dolorem quas labore? Delectus voluptatibus quibusdam maxime iure dolorem aliquid illo, velit corporis excepturi tempore voluptas laborum tempora voluptatem! Porro quaerat nulla quidem minus eveniet error repudiandae numquam ad quos aliquid quod ab dolores facere assumenda eaque quam, ipsum ea unde. Iste earum sunt tempore ab libero eius quae suscipit iure quas corrupti, necessitatibus consectetur impedit rerum aliquam neque animi sit nobis nam quam sequi natus explicabo doloremque.\n\nEa, expedita nemo. Repudiandae perspiciatis dolorum, animi aliquam reprehenderit optio dolore minus cumque temporibus assumenda dolor. Dolorem, voluptas!"
    )
);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS, post, get');
header('Access-Control-Max-Age', '3600');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
header('Access-Control-Allow-Credentials', 'true');

echo json_encode($output);

?>