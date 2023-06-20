<?php

require_once("../../../secure/scripts/teo_a_connect.php");

$data = ["gigId"=>(int)$_POST['gigId'], "date"=>$_POST['date'][0], "venue"=>(int)$_POST['venue'][0], "tickets"=>$_POST['tickets'][0]];

$result = [];

$query = "UPDATE Gigs SET
            date=:date,
            venue=:venue,
            tickets=:tickets
        WHERE gig_id = :gigId;";

try {
    $stmt = $db->prepare($query);
    $stmt->execute($data);
    $result['success'] = true;
}
catch (PDOException $e) {
    $result['success'] = false;
    $result['error'] =  "Database Error: " . $e->getMessage();
}

echo json_encode($result);

require_once("../../../secure/scripts/teo_disconnect.php");

?>