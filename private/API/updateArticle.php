<?php

require_once("../../../secure/scripts/teo_a_connect.php");

$parameters = [];
foreach ($_POST as $key=>$value) {
    switch ($key) {
        case 'article_content':
        case 'live_date':
        case 'article_id':
            $parameters[$key] = $value;
            break;
        default:
            break;
                }
            }

if ($parameters['live_date'] == '') $parameters['live_date'] = date('Y-m-d H:i:s');
else $parameters['live_date'] = str_replace('T', ' ', $parameters['live_date']);

$query = "UPDATE articles SET article_content=:article_content, live_date=:live_date WHERE article_id=:article_id;";

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

require_once("../../../secure/scripts/teo_disconnect.php");

?>