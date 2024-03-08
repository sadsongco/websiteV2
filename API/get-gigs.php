<?php

require_once('includes/api-header.php');
 
function getGigs ($db, $past=false) {
    $past_cond = $past ? 'WHERE date < CURDATE() ORDER BY order_date DESC' : 'WHERE date >= CURDATE() ORDER BY order_date ASC';
    try {
        $query = "SELECT Gigs.gig_id as gig_id,
            Gigs.date AS order_date,
            DATE_FORMAT(Gigs.date, '%D %b %Y') AS date,
            Gigs.tickets as tickets,
            Venues.name as venue,
            Venues.address as address,
            Venues.city as city,
            Venues.postcode as postcode,
            Venues.website as website,
            Countries.name as country
            FROM Gigs
            LEFT JOIN Venues ON Gigs.venue = Venues.venue_id
            LEFT JOIN Countries ON Countries.abv = Venues.country
            $past_cond";
        $gig_stmt = $db->prepare($query);
        $gig_stmt->execute();
        $gig_result = $gig_stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($gig_result as &$gig_details) {
            $gig_details['map_query'] = urlencode($gig_details['venue'].",".$gig_details['address'].",".$gig_details['city'].",".$gig_details['postcode'].",".$gig_details['country']);
        }
        if (sizeof($gig_result) > 0)
            return ['giglist'=>$gig_result];
        else
            return null;
    }
    catch (PDOException $e) {
        throw $e;
    }
}

$output = [
    "gigs"=>getGigs($db),
    "gigography"=>getGigs($db, true)
];

if (!$output['gigs'] && !$output['gigography']) exit("<h1>GIGS</h1>");

echo $m->render("gigs", $output);
