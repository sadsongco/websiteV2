<?php

require_once("../../../secure/scripts/teo_a_connect.php");

$query = "INSERT INTO Venues VALUES (0, :name, :address, :postcode, :city, :country, :website, :notes);";
$output = [];
try {
    $stmt = $db->prepare($query);
    $stmt->execute($_POST);
    $output['success'] = true;
} catch (PDOException $e) {
    $output['success'] = false;
    $output['error'] = "submitVenue DB error: ".$e->getMessage();
}

echo json_encode($output);



require_once("../../../secure/scripts/teo_disconnect.php");

?>