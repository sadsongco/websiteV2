<?php

require_once("../../secure/scripts/teo_connect.php");

try {
    $stmt = $db->prepare("SELECT * FROM Test_Content");
    $stmt->execute();
    
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

catch(PDOException $e) {
    echo "Database Error: " . $e->getMessage();
}

echo json_encode($result);

require_once("../../secure/scripts/teo_disconnect.php");

?>