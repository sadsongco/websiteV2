<?php

require_once("../../secure/scripts/teo_connect.php");
require '../lib/mustache.php-main/src/Mustache/Autoloader.php';
Mustache_Autoloader::register();

$m = new Mustache_Engine(array(
    'loader' => new Mustache_Loader_FilesystemLoader('../templates'),
    'partials_loader' => new Mustache_Loader_FilesystemLoader('../templates/partials')
));

function get_socials($db) {
    $query = "SELECT * FROM Socials ORDER BY pos ASC;";
    $result = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    return ["data"=>$result];
}

$output = get_socials($db);

echo $m->render("socials", $output);
require_once("../../secure/scripts/teo_disconnect.php");

?>