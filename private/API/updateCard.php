<?php

require_once("../../../../secure/scripts/teo_a_connect.php");

$data = json_decode(file_get_contents('php://input'));

echo json_encode($data);

require_once("../../../../secure/scripts/teo_disconnect.php");

?>