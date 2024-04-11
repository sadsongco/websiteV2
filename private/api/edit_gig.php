<?php

include_once('includes/private-api-header.php');

try {
    $query = "SELECT * FROM Gigs WHERE gig_id = ?;";
    $stmt = $db->prepare($query);
    $stmt->execute([$_GET['gig_id']]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
catch (PDOException $e) {
    exit($e->getMessage());
}

$params = ['gig_data'=>$result[0]];

echo $m->render('editGig', $params);