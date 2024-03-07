<?php

include_once("includes/private-api-header.php");
try {
    $query = "SELECT venue_id, name, city FROM Venues ORDER BY name ASC;";
    $result = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
}
catch (PDO_EXCEPTION $e) {
    exit("error retrieving venues: ".$e->getMessage());
}

if ($_GET['venue_id']) {
    foreach ($result AS &$venue) {
        $venue['selected'] = $_GET['venue_id'] == $venue['venue_id'] ? " selected" : "";
    }
}

$params = [];
$params['venues'] = $result;
echo $m->render('venueSelect', $params);