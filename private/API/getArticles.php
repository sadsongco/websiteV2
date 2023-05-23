<?php

require_once("../../../../secure/scripts/teo_a_connect.php");

try {
    $query = "SELECT * FROM Articles WHERE card_id = ? ORDER BY post_date DESC ";
    if ($_GET['content_type'] == 'single') {
        $query .= "LIMIT 1";
    }
    $stmt = $db->prepare($query);
    $stmt->execute([$_GET['card_id']]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result);
}
catch(PDOException $e) {
    echo "Database Error: " . $e->getMessage();
    exit();
}

require_once("../../../../secure/scripts/teo_disconnect.php");

?>