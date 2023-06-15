<?php

require_once("../../../secure/scripts/teo_a_connect.php");

$data = json_decode(file_get_contents('php://input'), true);
$parameters = [];
$return = [];
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
    $return['success'] = true;
}
catch (PDOException $e) {
    $return['success'] = false;
    $return['error'] = "Database Error: " . $e->getMessage();
}

echo json_encode($return);

require_once("../../../secure/scripts/teo_disconnect.php");

?>