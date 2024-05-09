<?php

require_once('../../../../secure/scripts/teo_a_connect.php');

require '../../../lib/mustache.php-main/src/Mustache/Autoloader.php';
Mustache_Autoloader::register();

$m = new Mustache_Engine(array(
    'loader' => new Mustache_Loader_FilesystemLoader('../assets/templates'),
    'partials_loader' => new Mustache_Loader_FilesystemLoader('../assets/templates/partials')
));

include_once("../../../email_management/includes/get_host.php");
