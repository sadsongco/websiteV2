<?php

require '../lib/mustache.php-main/src/Mustache/Autoloader.php';
Mustache_Autoloader::register();

$m = new Mustache_Engine(array(
    'loader' => new Mustache_Loader_FilesystemLoader('../templates'),
    'partials_loader' => new Mustache_Loader_FilesystemLoader('../templates/partials')
));

require_once("../../../../secure/scripts/teo_order_connect.php");
include_once("includes/p_2.php");

try {
    $query = "SELECT * FROM countries ORDER BY name ASC";
    $countries = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
}
catch (PDOException $e) {
    exit("Database error: ".$e->getMessage());
}

echo $m->render("newCustomerForm", ["countries"=>$countries]);

?>