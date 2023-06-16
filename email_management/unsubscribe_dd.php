<?php

include_once("./includes/html_head.php");

require_once("../../secure/scripts/teo_a_connect.php");

$message = "<p>Email unsubscribe page. You can access this through the link provided in your email";

if (isset($_GET['email']) && $_GET['email'] != '') {
    try {
        $stmt = $db->prepare("SELECT email_id FROM dd_cons_mailing_list WHERE email=?;");
        $stmt->execute([$_GET['email']]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $db_id = 0;
        if (isset($result) && isset($result[0]))
            $db_id = $result[0]['email_id'];
        $secure_id = hash('ripemd128', $_GET['email'].$db_id.'JamieAndNigel');
        if ($secure_id != $_GET['check']) {
            throw new PDOException('Bad Check Code', 1176);
        }
        $stmt = $db->prepare("DELETE FROM dd_cons_mailing_list WHERE email_id=? and email=?");
        $stmt->execute([$db_id, $_GET['email']]);
        $message = "<h2>Your email has been removed from the Dive Dive mailing list.</h2>";
    }
    catch(PDOException $e) {
        if ($e->getCode() != 1176) {
            error_log($e->getMessage(), $e->getCode());
            $message = "<p>Sorry, there was a background error</p>";
        }
        else {
            $message =  '<h2>'.$e->getMessage().' - please make sure you have accessed this page through the unsubscribe link provided in your email</h2>';
        }
    }
}

echo $message;

require_once("../../secure/scripts/teo_disconnect.php");

include_once("./includes/html_foot.php");

?>