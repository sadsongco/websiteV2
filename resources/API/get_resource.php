<?php

$output = [];
$sub_dir = '';

if ($_GET['resource'] == 'press_shots') $sub_dir = '/full_res/';

$output['path'] = 'resource_dirs/'.$_GET['resource'].'/';
$output['name'] = $_GET['resource'];
$output['resources'] = [];

try {
    if ($handle = opendir('../'.$output['path'].$sub_dir)) {
        while (false != ($entry = readdir($handle))) {
            if (substr($entry, 0, 1) == '.') continue;
            array_push($output['resources'], $entry);
        }
        closedir($handle);
    }

} catch (Exception $e) {
    $output['success'] = false;
    $output['error'] = $e->getMessage();
}



echo json_encode($output);

?>