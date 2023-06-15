<?php

require_once("../../../secure/scripts/teo_a_connect.php");

$data = json_decode(file_get_contents('php://input'), true);

$result = [];

$query = "DELETE FROM Gigs WHERE gig_id = ?";

try {
    $stmt = $db->prepare($query);
    $stmt->execute([$data['gig_id']]);
    $result['success'] = true;
}
catch (PDOException $e) {
    $result['success'] = false;
    $result['error'] = $e->getMessage();
}

echo json_encode($result);

require_once("../../../secure/scripts/teo_disconnect.php");

?>