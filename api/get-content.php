<?php

require_once("../../secure/scripts/teo_connect.php");
require '../lib/mustache.php-main/src/Mustache/Autoloader.php';
Mustache_Autoloader::register();

include_once("get-cards.php");

$m = new Mustache_Engine(array(
    'loader' => new Mustache_Loader_FilesystemLoader('../templates'),
    'partials_loader' => new Mustache_Loader_FilesystemLoader('../templates/partials')
));

$output = get_cards($db);

echo $m->render("card-template", $output);

?>