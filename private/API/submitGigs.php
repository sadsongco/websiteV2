<?php

require_once("../../../secure/scripts/teo_a_connect.php");

$data = json_decode(file_get_contents('php://input'), true);

$result = [];
foreach ($data as $parameters) {
    $query = "INSERT INTO Gigs VALUES (0, :date, :venue, :tickets, :city, :country, :address);";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute($parameters);
        $result['success'] = true;
    }
    catch (PDOException $e) {
        $result['success'] = false;
        $result['error'] =  "Database Error: " . $e->getMessage();
    }
}

echo json_encode($result);

require_once("../../../secure/scripts/teo_disconnect.php");

?>