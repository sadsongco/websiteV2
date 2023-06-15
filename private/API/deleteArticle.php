<?php

require_once("../../../secure/scripts/teo_a_connect.php");

$query = "DELETE FROM ArticleImages WHERE article_id=?;";
try {
    $stmt = $db->prepare($query);
    $stmt->execute([$_POST['article_id']]);
    $result['success'] = true;
}
catch (PDOException $e) {
    $result['success'] = false;
    $result['error'] =  "Database Error: " . $e->getMessage();
    return $result;
}

$query = "DELETE FROM Articles WHERE article_id=?;";

$result = [];
try {
    $stmt = $db->prepare($query);
    $stmt->execute([$_POST['article_id']]);
    $result['success'] = true;
}
catch (PDOException $e) {
    $result['success'] = false;
    $result['error'] =  "Database Error: " . $e->getMessage();
}

echo json_encode($result);

require_once("../../../secure/scripts/teo_disconnect.php");

?>