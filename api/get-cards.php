<?php

function get_cards($db) {
    $output =[];
    try {
        $stmt = $db->prepare("SELECT * FROM Cards ORDER BY card_pos ASC;");
        $stmt->execute();
        
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $output['success'] = true;
        $output['data'] = $result;
    }
    catch(PDOException $e) {
        $output['success'] = false;
        $output['message'] = "Database Error: " . $e->getMessage();
    }
    return $output;
}