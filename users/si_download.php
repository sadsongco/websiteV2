<?php

require_once("../../secure/scripts/teo_order_connect.php");

include_once("../api/includes/processors.php");

function getHost() {
    $protocol = 'http';
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') $protocol .= 's';
    return "$protocol://".$_SERVER['HTTP_HOST'];
}

define("__HOST__", getHost());
define("FILENAME", "TEO_SI.zip");
define("MEDIA_PATH", __DIR__. "/assets/media/");

function makeUniqueToken($token, $email) {
    return hash('sha1', $token.$email);
}

try {
    $query = "SELECT customer_id FROM Customers WHERE email = ?;";
    $stmt = $db->prepare($query);
    $stmt->execute([$_GET['email']]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
catch (PDOException $e) {
    exit ("Database error: ".$e->getMessage());
}

if ($stmt->rowCount() == 0) exit("email not found");

$token = $result[0]['customer_id'];
$u_token = makeUniqueToken($token, $_GET['email']);

try {
    $query = "SELECT * FROM download_tokens WHERE customer_id = ? AND token = ?;";
    $stmt = $db->prepare($query);
    $stmt->execute([$token, $u_token]);
    $stmt->fetch();
    $result = $stmt->rowCount();
}
catch (PDOException $e) {
    exit ("Sorry, there was a technical error. Please contact nigel@theexactopposite.uk");
}

if ($result == 0) exit("Token already downloaded");

if ($result > 0) {
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private",false);
    header("Content-Type: application/zip");
    header("Content-Disposition: attachment; filename=".FILENAME);
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".filesize(MEDIA_PATH.FILENAME));
    readfile(MEDIA_PATH.FILENAME);
}

$query = "DELETE FROM download_tokens WHERE token = ?;";
$stmt = $db->prepare($query);
$stmt->execute([$u_token]);
if ($stmt->rowCount() < 1) {
    exit ("Database error removing token");
}