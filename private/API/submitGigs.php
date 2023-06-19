<?php

require_once("../../../secure/scripts/teo_a_connect.php");

// $data = json_decode(file_get_contents('php://input'), true);

$result = [];
foreach ($_POST['date'] as $key=>$value) {
    $query = "INSERT INTO Gigs VALUES (0, ?, ?, ?);";
    $parameters = [$_POST['date'][$key], $_POST['tickets'][$key], $_POST['venue'][$key]];
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