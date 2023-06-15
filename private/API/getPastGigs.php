<?php

require_once("../../../secure/scripts/teo_a_connect.php");

try {
    $query = "SELECT * FROM Gigs WHERE date < CURDATE() ORDER BY date DESC";
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