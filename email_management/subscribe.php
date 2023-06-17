<?php
// **************** SUBSCRIBE TO TEO MAILING LIST ****************
// ************ THIS IS ONLY ACCESSED FROM DD MAILOUT ************

include_once("./includes/html_head.php");

require_once("../../secure/scripts/teo_a_connect.php");

function getCheckCode($db, $email) {
    try {
        $stmt = $db->prepare("SELECT email_id FROM dd_cons_mailing_list WHERE email=?;");
        $stmt->execute([$email]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $db_id = $result[0]['email_id'];
        $secure_id = hash('ripemd128', $email.$db_id.'JamieAndNigel');
        return $secure_id;
    }
    catch(PDOException $e) {
        return null;
    }
}

$message = "<p>Mailing list subscription page. Please access this through the link in your email.</p>";

// local host server
$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https'?'https':'http';
$host = "$protocol://".$_SERVER['HTTP_HOST'];

if (isset ($_POST['add_name']) && $_POST['add_name'] == "Add Your Name") {
    try {
        $secure_id = getCheckCode($db, $_POST['email']);
        if ($secure_id == null) throw new PDOException('get check code DB Error', 1078);
        if ($secure_id != $_POST['check']) throw new PDOException('Bad Check Code', 1176);
        $stmt = $db->prepare("UPDATE mailing_list SET name=? WHERE email_id=? and email=?");
        $stmt->execute([$_POST['name'], $db_id, $_POST['email']]);
        $message = "<h2>Name updated!</h2>";
        $_GET['email'] = $_POST['email'];
        $_GET['check'] = $_POST['check'];
    }
    catch(PDOException $e) {
        error_log($e->getMessage());
        if ($e->getCode() != 1176) {
            $message = "<p>Sorry, there was a background error</p>";
        }
        else {
            $message =  '<h2>'.$e->getMessage().' - please make sure you have accessed this page through the add name form below</h2>';
        }
    }
}

elseif (isset($_GET['email']) && $_GET['email'] != '' && isset($_GET['check']) && $_GET['check'] != '') {
    try {
        $secure_id = getCheckCode($db, $_GET['email']);
        if ($secure_id == null) throw new PDOException('get check code DB Error', 1078);
        if ($secure_id != $_GET['check']) throw new PDOException('Bad Check Code', 1176);
        $stmt = $db->prepare("INSERT INTO mailing_list (email, name, domain, subscribed, confirmed, date_added) VALUES (?, ?, SUBSTRING_INDEX(?, '@', -1), ?, ?, NOW());");
        $stmt->execute([$_GET['email'], '', $_GET['email'], 1, 1]);
        $_GET['check'] = hash('ripemd128', $_GET['email'].$db->lastInsertId().'JamieAndNigel');
        $message = '<p>The email <span class = "email">'.$_GET['email'].'</span> has been added to the Unbelievable Truth mailing list.<br />';
    }
    catch(PDOException $e) {
        error_log($e->getMessage());
        if ($e->getCode() == 1176) {
            $message =  '<h2>'.$e->getMessage().'- please make sure you have accessed this page through the link in your email.</h2>';
        }
        elseif ($e->getCode() != 23000) {
            $message = "<p>Sorry, there was a background error</p>";}
        else {
            $stmt = $db->prepare("UPDATE mailing_list SET subscribed=1 WHERE email=?");
            $stmt->execute([$_GET['email']]);
            $stmt = $db->prepare("SELECT email_id FROM mailing_list WHERE email=?");
            $stmt->execute([$_GET['email']]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $id = 0;
            if (isset($result) && isset($result[0]))
                $id = $result[0]['email_id'];
            $_GET['check'] = hash('ripemd128', $_GET['email'].$id.'JamieAndNigel');
            $message = '<p>That email is already on our list, thank you!</p>';
        }
    }
    $message .= 'If you would like to add your name to your email on The Exact Opposite mailing list so we can be more polite when we contact you, feel free to do so here:<br />
        <form action = "'.$_SERVER['PHP_SELF'].'" method = "post">
            <input type = "text" name = "name" size = "30" placeholder = "your name" />
            <input type="submit" name="add_name" value="Add Your Name" />
            <input type="hidden" name="check" value = "'.$_GET['check'].'" />
            <input type="hidden" name="email" value = "'.$_GET['email'].'" />
        </form>
        <footer>
            If you want to unsubscribe click &nbsp;<a href="'.$host.'/email_management/unsubscribe.php?email='.$_GET['email'].'&check='.$_GET['check'].'">HERE</a><br />
        </footer>
';
}

require_once("../../secure/scripts/teo_disconnect.php");

echo $message;

include_once("./includes/html_foot.php");
?>
