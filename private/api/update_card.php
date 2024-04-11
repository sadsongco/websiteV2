<?php

include_once('includes/private-api-header.php');

try {
    $query = "UPDATE Cards SET
        title = :title,
        strap = :strap
        WHERE card_id = :card_id;";
    $stmt = $db->prepare($query);
    $stmt->execute($_POST);
}
catch (PDO_EXCEPTION $e) {
    die('Error updating card: '.$e->getMessage());
}

header("HX-Trigger: cardUpdated");
echo "<h2>Card Updated</h2>";