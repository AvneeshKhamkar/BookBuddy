<?php
session_start();
include "db_connect.php";

$user_id=$_SESSION['user_id'];

$conn->query("DELETE FROM cart WHERE user_id=$user_id");

echo json_encode([
"status"=>"success",
"message"=>"Cart cleared"
]);
?>