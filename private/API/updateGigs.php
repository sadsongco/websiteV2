<?php

require_once("../../../secure/scripts/teo_a_connect.php");

$data = json_decode(file_get_contents('php://input'), true);

$result = [];

$query = "UPDATE Gigs SET
            date=:date,
            venue=:venue,
            tickets=:tickets,
            city=:city,
            country=:country,
            address=:address
        WHERE gig_id = :gigId;";

try {
    $stmt = $db->prepare($query);
    $stmt->execute($data[0]);
    $result['success'] = true;
}
catch (PDOException $e) {
    $result['success'] = false;
    $result['error'] =  "Database Error: " . $e->getMessage();
}

echo json_encode($result);

require_once("../../../secure/scripts/teo_disconnect.php");

?>