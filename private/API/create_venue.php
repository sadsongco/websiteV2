<?php

include_once('includes/private-api-header.php');

try {
    $query = "INSERT INTO Venues VALUES (0, :name, :address, :postcode, :city, :country, :website, :notes);";
    $stmt = $db->prepare($query);
    $stmt->execute($_POST);
}

catch (PDOException $e) {
    exit($m->render('newVenueButton', ["error"=>true, "msg"=>"Database error: ".$e->getMessage()]));
}

header("HX-Trigger: venueAdded");
echo $m->render('newVenueButton', ["msg"=>"Venue added"]);