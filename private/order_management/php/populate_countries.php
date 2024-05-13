<?php

require_once("../../../../secure/scripts/teo_order_connect.php");
include_once("includes/p_2.php");

$postal_dir = "../assets/postal_zones";

if ($handle = opendir($postal_dir)) {
    while (false !== ($entry = readdir($handle))) {
        $zone_id = null;
        if (substr($entry, 0, 1) == ".") continue;
        $name = explode("_", $entry)[0];
        // $name = str_replace("_", " ", $entry);
        $name = str_replace(".txt", "", $name);
        p_2($name);
        // Insert postal zone into database if not already there
        try {
            $query = "INSERT INTO postal_zones VALUES (NULL, ?)";
            $stmt = $db->prepare($query);
            $stmt->execute([$name]);
            $zone_id = $db->lastInsertId();
        }
        catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                echo "<br>--Postal zone exists, continuing...--<br>";
                $query = "SELECT id FROM postal_zones WHERE name = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$name]);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $zone_id = $result[0]['id'];
            }
            else (exit("Database error:".$e->getMessage()));
        }
        echo "<br>--ID is $zone_id--<br>";
        $countries = file($postal_dir."/".$entry);
        foreach ($countries AS $country) {
            try {
                $query = "INSERT INTO countries VALUES (NULL, ?, ?);";
                $stmt = $db->prepare($query);
                $stmt->execute([trim($country), $zone_id]);
                p_2($country." inserted into database");
            }
            catch (PDOException $e) {
                if ($e->getCode() == 23000) echo "<br>--$country already in database--";
                else echo "<br>--Database error: ".$e->getMessage()."--<br>";
            }
        }
    }
    closedir($handle);
}