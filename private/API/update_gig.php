<?php

include_once('includes/private-api-header.php');

try {
    $query = "UPDATE Gigs SET date = :date, tickets = :tickets, venue = :venue WHERE gig_id = :gig_id;";
    $stmt = $db->prepare($query);
    $stmt->execute($_POST);
}
catch (PDO_EXCEPTION $e) {
    die('Error updating gig: '.$e->getMessage());
}

header("HX-Trigger: gigUpdated");
echo "<h2>Gig Updated</h2>";