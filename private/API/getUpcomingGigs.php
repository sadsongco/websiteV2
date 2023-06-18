<?php

require_once("../../../secure/scripts/teo_a_connect.php");

try {
    $query = "SELECT Gigs.gig_id as gig_id,
    Gigs.date as date,
    Gigs.tickets as tickets,
    Venues.name as venue,
    Venues.address as address,
    Venues.city as city,
    Countries.name as country
    FROM Gigs
    LEFT JOIN Venues ON Gigs.venue = Venues.venue_id
    LEFT JOIN Countries ON Countries.abv = Venues.country
    WHERE date >= CURDATE() ORDER BY date DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result);
}
catch(PDOException $e) {
    $output = ["SUCCESS"=>false, "MESSAGE"=>"Database Error: " . $e->getMessage()];
    echo json_encode($output);
    exit();
}

require_once("../../../secure/scripts/teo_disconnect.php");

?>