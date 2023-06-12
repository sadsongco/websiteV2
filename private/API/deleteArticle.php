<?php

require_once("../../../../secure/scripts/teo_a_connect.php");

$data = json_decode(file_get_contents('php://input'), true);
$parameters = [];
foreach ($data as $value) {
    if (key($value) == 'article_id')
    $parameters[key($value)] = (int)$value[key($value)];
}

$query = "DELETE FROM articles WHERE article_id=:article_id;";

$result = [];
try {
    $stmt = $db->prepare($query);
    $stmt->execute($parameters);
    $result['success'] = true;
}
catch (PDOException $e) {
    $result['success'] = false;
    $result['error'] =  "Database Error: " . $e->getMessage();
}

echo json_encode($result);

require_once("../../../../secure/scripts/teo_disconnect.php");

?>