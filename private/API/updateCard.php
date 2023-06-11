<?php

require_once("../../../../secure/scripts/teo_a_connect.php");

$data = json_decode(file_get_contents('php://input'), true);
$parameters = [];
foreach ($data as $value) {
    switch (key($value)) {
        case 'title':
        case 'strap':
        case 'card_id':
            $parameters[key($value)] = $value[key($value)];
            break;
        default:
            break;
    }
}

$query = "UPDATE Cards SET title = :title, strap = :strap WHERE card_id = :card_id";

try {
    $stmt = $db->prepare($query);
    $stmt->execute($parameters);
    echo "SUCCESS";
}
catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage();
}

require_once("../../../../secure/scripts/teo_disconnect.php");

?>