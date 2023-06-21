<?php

$output = [];
$sub_dir = '';

if ($_GET['resource'] == 'press_shots') $sub_dir = '/full_res/';

$output['path'] = $_GET['resource'];
$output['resources'] = [];

if ($handle = opendir('../resource_dirs/'.$output['path'].$sub_dir)) {
    while (false != ($entry = readdir($handle))) {
        if (str_starts_with($entry, '.')) continue;
        array_push($output['resources'], $entry);
    }
}

echo json_encode($output);

?>