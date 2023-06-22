<?php

require_once("../../../secure/scripts/teo_a_connect.php");

try {
    $query = "SELECT venue_id, Venues.name, city, Countries.name as country
    FROM Venues
    LEFT JOIN Countries on Countries.abv = Venues.country";
    $stmt = $db->query($query);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result);
} catch (PDOException $e) {
    error_log('GetVenues DB error: '. $e->getMessage());
    return null;
}

require_once("../../../secure/scripts/teo_disconnect.php");

?>