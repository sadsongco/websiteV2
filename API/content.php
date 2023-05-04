<?php

require_once("../../../secure/scripts/teo_connect.php");

function getSingleContent($db, $card_id) {
    try {
        $stmt = $db->prepare("SELECT article_content FROM Articles WHERE card_id = ? ORDER BY live_date DESC LIMIT 1;");
        $stmt->execute(array($card_id));
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (sizeof($result) > 0)
            return $result[0];
        else
            return null;
    }
    catch (PDOException $e) {
        echo "Database Error: " . $e->getMessage();        
    }
}

function get_cards($db) {
    try {
        $stmt = $db->prepare("SELECT * FROM Cards ORDER BY card_pos ASC;");
        $stmt->execute();
        
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $output =[];
        foreach ($result as $row) {
            switch($row['content_type']) {
                case 'single':
                    $row['content'] = getSingleContent($db, $row['card_id']);
                    array_push($output, $row);
                    break;
            }
        }
    }
    
    catch(PDOException $e) {
        echo "Database Error: " . $e->getMessage();
    }
    return $output;
}

$output = get_cards($db);
echo json_encode($output);

require_once("../../../secure/scripts/teo_disconnect.php");

?>