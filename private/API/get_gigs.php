<?php

include_once("includes/private-api-header.php");
try {
    $query = "SELECT
        gig_id,
        Gigs.date as order_date,
        DATE_FORMAT(date, '%D %b %Y') AS date,
        tickets,
        name,
        address,
        city,
        postcode,
        country
    FROM Gigs
    LEFT JOIN Venues ON Venues.venue_id = Gigs.venue
    ORDER BY order_date DESC;";
    $result = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
}
catch (PDO_EXCEPTION $e) {
    exit ('Error retrieving gigs: '.$e->getMessage());
}

$params = [];
$params['gigs'] = $result;

echo $m->render('gigs', $params);