<?php

require_once("../../../secure/scripts/teo_a_connect.php");

try {
    $query = "SELECT abv as id, name FROM Countries";
    $stmt = $db->query($query);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result);
} catch (PDOException $e) {
    error_log('GetCountries DB error: '. $e->getMessage());
    return null;
}

?>