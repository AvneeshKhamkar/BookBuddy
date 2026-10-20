<?php
session_start();
include "db_connect.php";

$user_id=$_SESSION['user_id'];
$cart_id=$_POST['cart_id'];

$conn->query("DELETE FROM cart WHERE id=$cart_id AND user_id=$user_id");

echo json_encode([
"status"=>"success",
"message"=>"Item removed from cart"
]);
?>