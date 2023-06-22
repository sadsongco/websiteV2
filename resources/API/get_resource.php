<?php

$output = [];
$sub_dir = '';

if ($_GET['resource'] == 'press_shots') $sub_dir = '/full_res/';

$output['path'] = 'resource_dirs/'.$_GET['resource'].'/';
$output['name'] = $_GET['resource'];
$output['resources'] = [];

if ($handle = opendir('../'.$output['path'].$sub_dir)) {
    while (false != ($entry = readdir($handle))) {
        if (str_starts_with($entry, '.')) continue;
        array_push($output['resources'], $entry);
    }
    closedir($handle);
}


echo json_encode($output);

?>