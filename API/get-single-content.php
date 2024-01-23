<?php

function getSingleContent($db, $card_id) {
    $output = [];
    try {
        $stmt = $db->prepare("SELECT article_id, article_content
        FROM Articles
        WHERE card_id = ?
        AND live_date <= NOW()
        ORDER BY live_date
        DESC LIMIT 1;");
        $stmt->execute(array($card_id));
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $output = [];
        if (sizeof($result) > 0)
            return $result[0];
        else
            return null;
    }
    catch (PDOException $e) {
        $output['success'] = false;

        $output['message'] = "Database Error: " . $e->getMessage();   
    }
}

?>