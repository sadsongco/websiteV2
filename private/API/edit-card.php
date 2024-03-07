<?php

include_once('includes/private-api-header.php');

try {
    $query = "SELECT * FROM Cards WHERE card_id = ?;";
    $stmt = $db->prepare($query);
    $stmt->execute([$_GET['card_id']]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
catch (PDOException $e) {
    echo $e->getMessage();
}
$params['card-info'] = $result[0];
$params['max-size'] = return_bytes(ini_get("upload_max_filesize"));
$params['session_upload_name'] = ini_get("session.upload_progress.name");


if ($_GET['card_id'] == "gigs") {
    echo $m->render('editGigs', $params);
} else {
    echo $m->render('editCard', $params); 
}
