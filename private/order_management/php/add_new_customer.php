<?php

require_once("../../../../secure/scripts/teo_order_connect.php");
include_once("includes/p_2.php");

try {
    $query = "INSERT INTO Customers VALUES (0, :name, :address_1, :address_2, :city, :postcode, :country, :email);";
    $stmt = $db->prepare($query);
    $stmt->execute($_POST);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        $query = "SELECT customer_id FROM Customers WHERE name = ? AND postcode = ?;";
        $stmt = $db->prepare($query);
        $stmt->execute([$_POST['name'], $_POST['postcode']]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header ('HX-Trigger:{"existingCustomer":"'.$result[0]['customer_id'].'"}');
        exit("That customer already exists - selected for order");
    }
    echo "Database error: ".$e->getMessage();
}

header ('HX-Trigger:updateOrderForm');
header ('HX-Trigger-After-Settle:clearCustomerForm');
echo "New Customer Added";
