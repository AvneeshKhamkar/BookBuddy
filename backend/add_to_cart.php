<?php
session_start();
include "db_connect.php";

if(!isset($_SESSION['user_id'])){
echo json_encode(["status"=>"error","message"=>"Login required"]);
exit;
}

$user_id=$_SESSION['user_id'];
$book_id=$_POST['book_id'];

$check=$conn->prepare("SELECT * FROM cart WHERE user_id=? AND book_id=?");
$check->bind_param("ii",$user_id,$book_id);
$check->execute();
$result=$check->get_result();

if($result->num_rows>0){

$conn->query("UPDATE cart SET quantity=quantity+1 
WHERE user_id=$user_id AND book_id=$book_id");

echo json_encode([
"status"=>"success",
"message"=>"Quantity updated in cart"
]);

}else{

$stmt=$conn->prepare("INSERT INTO cart(user_id,book_id,quantity) VALUES(?,?,1)");
$stmt->bind_param("ii",$user_id,$book_id);
$stmt->execute();

echo json_encode([
"status"=>"success",
"message"=>"Book added to cart"
]);

}
?>