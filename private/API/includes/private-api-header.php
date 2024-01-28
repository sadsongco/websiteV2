<?php

require_once("../../../secure/scripts/teo_connect.php");
require '../../lib/mustache.php-main/src/Mustache/Autoloader.php';
Mustache_Autoloader::register();

$m = new Mustache_Engine(array(
    'loader' => new Mustache_Loader_FilesystemLoader('templates'),
    'partials_loader' => new Mustache_Loader_FilesystemLoader('templates/partials')
));

include_once('../../api/includes/processors.php');

date_default_timezone_set('Europe/London');

function return_bytes($val)
{
    preg_match('/(?<value>\d+)(?<option>.?)/i', trim($val), $matches);
    $inc = array(
        'g' => 1073741824, // (1024 * 1024 * 1024)
        'm' => 1048576, // (1024 * 1024)
        'k' => 1024
    );
    
    $value = (int) $matches['value'];
    $key = strtolower(trim($matches['option']));
    if (isset($inc[$key])) {
        $value *= $inc[$key];
    }

    return $value;
}