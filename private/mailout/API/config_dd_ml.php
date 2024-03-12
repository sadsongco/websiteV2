<?php

require_once('includes/mailout_includes.php');

$query = "SELECT * FROM dd_cons_mailing_list;";
$result = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);

$sql = "CREATE TABLE IF NOT EXISTS `dd_mailing_list` (
    `email_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(127) NOT NULL UNIQUE,
    `domain` VARCHAR(100) NOT NULL,
    `name` VARCHAR(100),
    `last_sent` MEDIUMINT NOT NULL DEFAULT 0,
    `subscribed` TINYINT(1) NOT NULL DEFAULT 1,
    `confirmed` TINYINT(1) NOT NULL DEFAULT 1,
    `date_added` DATE,
    `error` TINYINT(1) DEFAULT 0
    ) CHARACTER SET utf8 COLLATE utf8_general_ci;";
try {
    $db->exec($sql);
}
catch (PDO_EXCEPTION $e) {
    exit($sql."<br><br>".$e->getMessage());
}


foreach($result as $row) {
    $domain = explode('@', $row['email'])[1];
    $query = "INSERT INTO dd_mailing_list VALUES (?, ?, ?, NULL, 0, 1, 1, NULL, 0);";    
    $params = [$row['email_id'], $row['email'], $domain];
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    echo $row['email']." added<br />";
}