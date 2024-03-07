<?php

include_once('includes/private-api-header.php');

try {
    $query = "DELETE FROM Gigs WHERE gig_id=?;";
    $stmt = $db->prepare($query);
    $stmt->execute([$_POST['gig_id']]);
    $message = "Gig Deleted";
} catch (Exception $e) {
    $message = "Gig couldn't be deleted: ".$e->getMessage();
}

header("HX-Trigger: gigDeleted");
echo $message;