<?php

require_once("../../../secure/scripts/teo_a_connect.php");

try {
    $stmt = $db->prepare("UPDATE Test_Content SET content=? WHERE target='about';");
    $stmt->execute(["!!!UPDATED ". date("d:m:Y, H:i:sa"). " ABOUT :::: Lorem ipsum dolor sit amet consectetur adipisicing elit. Corrupti voluptatem soluta sequi enim omnis cupiditate. Voluptatum, quos aut libero a obcaecati expedita nam, facere adipisci eligendi debitis velit iusto tenetur?"]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
catch (PDOException $e) {
    echo("DB ERROR: ". $e->getMessage());
}

echo json_encode($result);

require_once("../../../secure/scripts/teo_disconnect.php");

?>