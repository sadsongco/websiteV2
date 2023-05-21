<?php

require_once("../../../../secure/scripts/teo_a_connect.php");

$query = 'UPDATE Cards SET card_pos = CASE card_id';
$update_variables = [];
$reset_variables = [];
$reset_counter = count($_GET) + 1;
foreach ($_GET as $card_id=>$card_pos) {
    $query .= " WHEN ? THEN ?";
    array_push($update_variables, $card_id);
    array_push($update_variables, $card_pos);
    array_push($reset_variables, $card_id);
    array_push($reset_variables, $reset_counter);
    $reset_counter++;
}
$query .= "END;";

try {
    $stmt = $db->prepare($query);
    $stmt->execute($reset_variables);
    $stmt = $db->prepare($query);
    $stmt->execute($update_variables);
}
catch(PDOException $e) {
    echo "Database Error: " . $e->getMessage();
    exit();
}

require_once("../../../../secure/scripts/teo_disconnect.php");

?>