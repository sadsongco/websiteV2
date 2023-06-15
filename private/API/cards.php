<?php

require_once("../../../secure/scripts/teo_connect.php");

try {
    $stmt = $db->prepare("SELECT * FROM Cards ORDER BY card_pos ASC;");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
    echo "Database Error: " . $e->getMessage();
    exit();
}

echo json_encode($result);

require_once("../../../secure/scripts/teo_disconnect.php");

?>