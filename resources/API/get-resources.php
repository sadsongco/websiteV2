<?php

require_once("../../../secure/scripts/teo_connect.php");
require '../../lib/mustache.php-main/src/Mustache/Autoloader.php';
Mustache_Autoloader::register();

$m = new Mustache_Engine(array(
    'loader' => new Mustache_Loader_FilesystemLoader('templates'),
    'partials_loader' => new Mustache_Loader_FilesystemLoader('templates/partials')
));

function p_2($input) {
    echo "<pre>"; print_r($input); echo "</pre>";
}

include("get-resource.php");

$resource_sections = ['bio', 'playlists', 'press_shots', 'logos', 'tech_spec'];


foreach ($resource_sections AS $resource_section) {
    $resources = getResource($resource_section);
    echo $m->render('resourceSection', $resources);
}