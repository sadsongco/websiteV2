<?php

require_once('../../../../secure/scripts/teo_a_connect.php');

echo "<pre>hello\n\n";

$query = "SELECT email FROM dd_tmp_mailing_list;";

$stmt = $db->query($query);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($result as $row) {
    $query = "INSERT INTO dd_mailing_list VALUES (0, ?);";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute([$row['email']]);
    } catch (PDOException $e) {
        echo $e->getMessage()."\n";
    }
}

echo "done</pre>";

include_once('../../../../secure/scripts/teo_disconnect.php');

?>