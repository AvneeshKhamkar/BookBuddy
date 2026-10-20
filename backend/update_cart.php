<?php

include "db_connect.php";

$cart_id = $_POST['cart_id'];
$quantity = $_POST['quantity'];

$stmt = $conn->prepare("UPDATE cart SET quantity=? WHERE id=?");
$stmt->bind_param("ii",$quantity,$cart_id);

if($stmt->execute()){
echo json_encode(["status"=>"success","message"=>"Cart updated"]);
}else{
echo json_encode(["status"=>"error","message"=>"Update failed"]);
}

?>