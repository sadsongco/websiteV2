<?php

require_once("../../../../secure/scripts/teo_a_connect.php");

$data = json_decode(file_get_contents('php://input'), true);
$parameters = [];
foreach ($data as $value) {
    switch (key($value)) {
        case 'article_content':
        case 'live_date':
        case 'card_id':
            $parameters[key($value)] = $value[key($value)];
            break;
        default:
            break;
    }
}

if ($parameters['live_date'] == '') $parameters['live_date'] = date('Y-m-d H:i:s');
else $parameters['live_date'] = str_replace('T', ' ', $parameters['live_date']).":00";

$query = "INSERT INTO articles VALUES (0, :article_content, NOW(), :live_date, :card_id);";

$result = [];
try {
    $stmt = $db->prepare($query);
    // $stmt->execute($parameters);
    $result['success'] = true;
}
catch (PDOException $e) {
    $result['success'] = false;
    $result['error'] =  "Database Error: " . $e->getMessage();
}

echo json_encode($result);

require_once("../../../../secure/scripts/teo_disconnect.php");

?>