<?php

include_once('includes/private-api-header.php');

$result = "NOTHING HAPPENED";

try {
    $query = "SELECT `abv`, `name` FROM `Countries` ORDER BY `name` ASC;";
    $result = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
}
catch (PDOException $e) {
    echo $e->getMessage();
}

foreach ($result as &$country) {
    $country['selected'] = "";
    if ($country['abv'] == "UK") $country['selected'] = " selected";
}

echo $m->render('countrySelect', ["countries"=>$result]);