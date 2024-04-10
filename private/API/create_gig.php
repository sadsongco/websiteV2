<?php

include_once('includes/private-api-header.php');

try {
    $query = "INSERT INTO Gigs VALUES (NULL, :date, :tickets, :venue, :event);";
    $stmt = $db->prepare($query);
    $stmt->execute($_POST);
}

catch (PDOException $e) {
    exit("Database error: ".$e->getMessage());
}

header("HX-Trigger: gigAdded");
echo "<h2>Gig added</h2>";