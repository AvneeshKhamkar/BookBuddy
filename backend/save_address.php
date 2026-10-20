<?php

session_start();
include "db_connect.php";

$user_id = $_SESSION['user_id'];

$full_name = $_POST['full_name'];
$phone = $_POST['phone'];
$address = $_POST['address'];
$city = $_POST['city'];
$pincode = $_POST['pincode'];

$stmt = $conn->prepare("
INSERT INTO addresses(user_id,full_name,phone,address,city,pincode)
VALUES(?,?,?,?,?,?)
");

$stmt->bind_param("isssss",$user_id,$full_name,$phone,$address,$city,$pincode);

if($stmt->execute()){

$_SESSION['last_address_id'] = $conn->insert_id;

echo json_encode(["status"=>"success"]);

}else{

echo json_encode(["status"=>"error"]);

}

?>