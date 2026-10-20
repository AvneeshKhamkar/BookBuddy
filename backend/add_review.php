<?php

session_start();
include "db_connect.php";

$user_id = $_SESSION['user_id'];

$book_id = $_POST['book_id'];
$rating = $_POST['rating'];
$review = $_POST['review'];

/* Check book owner */

$stmt = $conn->prepare("SELECT user_id FROM books WHERE id=?");
$stmt->bind_param("i",$book_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

if($row['user_id'] == $user_id){

echo json_encode([
"status"=>"error",
"message"=>"You cannot review your own book"
]);

exit;

}


$stmt = $conn->prepare("INSERT INTO reviews(book_id,user_id,rating,review) VALUES(?,?,?,?)");
$stmt->bind_param("iiis",$book_id,$user_id,$rating,$review);

if($stmt->execute()){

echo json_encode([
"status"=>"success",
"message"=>"Review submitted successfully"
]);

}else{

echo json_encode([
"status"=>"error",
"message"=>"Failed to submit review"
]);

}

?>