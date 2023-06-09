<?php

require_once("../../secure/scripts/teo_connect.php");

function getGigs ($db) {
    try {
        $query = "SELECT Gigs.gig_id as gig_id,
            DATE_FORMAT(Gigs.date, '%D %b %Y') AS date,
            Gigs.tickets as tickets,
            Venues.name as venue,
            Venues.address as address,
            Venues.city as city,
            Venues.postcode as postcode,
            Venues.website as website,
            Countries.name as country
            FROM Gigs
            LEFT JOIN Venues ON Gigs.venue = Venues.venue_id
            LEFT JOIN Countries ON Countries.abv = Venues.country
            WHERE date >= CURDATE()
            ORDER BY date ASC";
        $gig_stmt = $db->prepare($query);
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

function getArticleImages($db, $article_id) {
    try {
        $img_stmt = $db->prepare("SELECT * FROM ArticleImages WHERE article_id = ?");
        $img_stmt->execute(array($article_id));
        $img_result = $img_stmt->fetchAll(PDO::FETCH_ASSOC);
        if (sizeof($img_result) > 0)
            return $img_result;
        else
            return null;
    }
    catch (PDOException $e) {
        echo "Database Error: " . $e->getMessage();     
    }
}

function getSingleContent($db, $card_id) {
    try {
        if ($card_id == 'gigs') {
            return getGigs($db);
        }
        $stmt = $db->prepare("SELECT article_id, article_content
        FROM Articles
        WHERE card_id = ?
        AND live_date <= NOW()
        ORDER BY live_date
        DESC LIMIT 1;");
        $stmt->execute(array($card_id));
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $output = [];
        foreach ($result as $row) {
            $row['images'] = getArticleImages($db, $row['article_id']);
            array_push($output, $row);
        }
        if (sizeof($result) > 0)
        return $output;
        else
        return null;
    }
    catch (PDOException $e) {
        echo "Database Error: " . $e->getMessage();        
    }
}

function getMultiContent($db, $card_id) {
    try {
        $stmt = $db->prepare("SELECT article_id,
        article_content,
        DATE_FORMAT(live_date, '%D %b %Y, %H:%i') AS live_date
        FROM Articles
        WHERE card_id = ?
        AND live_date <= NOW()
        ORDER BY live_date ASC;");
        $stmt->execute(array($card_id));
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (sizeof($result) == 0) return null;
        $output = [];
        foreach ($result as $row) {
            $row['images'] = getArticleImages($db, $row['article_id']);
            array_push($output, $row);
        }
        return $output;        
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

require_once("../../secure/scripts/teo_disconnect.php");

?>