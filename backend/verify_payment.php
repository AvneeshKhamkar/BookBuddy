<?php

session_start();
include "db_connect.php";

$payment_id = $_GET['payment_id'] ?? '';

$user_id = $_SESSION['user_id'];
$amount = $_SESSION['payment_amount'] ?? 0;
$address_id = $_SESSION['last_address_id'] ?? 0;

// Safety check
if(empty($payment_id)){
    die("Payment ID missing");
}

// Insert into DB
$stmt = $conn->prepare("
INSERT INTO payments(user_id, amount, payment_id, address_id)
VALUES(?, ?, ?, ?)
");

$stmt->bind_param("iisi", $user_id, $amount, $payment_id, $address_id);

if($stmt->execute()){
    header("Location: ../frontend/payment_success.php?payment_id=".$payment_id);
}else{
    echo "Database error: " . $stmt->error;
}

?>