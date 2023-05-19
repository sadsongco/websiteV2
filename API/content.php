<?php

require_once("../../../secure/scripts/teo_connect.php");

function getGigs ($db) {
    try {
        $gig_stmt = $db->prepare("SELECT * FROM Gigs WHERE DATE(date) > CURDATE();");
        $gig_stmt->execute();
        $gig_result = $gig_stmt->fetchAll(PDO::FETCH_ASSOC);
        if (sizeof($gig_result) > 0)
            return $gig_result;
        else
            return null;
    }
    catch (PDOException $e) {
        throw $e;
    }
}

function getSingleContent($db, $card_id) {
    try {
        if ($card_id == 'gigs') {
            return getGigs($db);
        }
        $stmt = $db->prepare("SELECT article_content FROM Articles WHERE card_id = ? ORDER BY live_date DESC LIMIT 1;");
        $stmt->execute(array($card_id));
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (sizeof($result) > 0)
            return $result;
        else
            return null;
    }
    catch (PDOException $e) {
        echo "Database Error: " . $e->getMessage();        
    }
}

function getMultiContent($db, $card_id) {
    try {
        $stmt = $db->prepare("SELECT article_content, DATE_FORMAT(live_date, '%D %b %Y, %H:%i') AS live_date FROM Articles WHERE card_id = ? ORDER BY live_date DESC;");
        $stmt->execute(array($card_id));
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (sizeof($result) > 0)
        return $result;
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
                case 'multi-inline':
                case 'multi-paged':
                    $row['content'] = getMultiContent($db, $row['card_id']);
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