<?php
session_start();
include 'db_connect.php';

header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
echo json_encode([
"status"=>"error",
"message"=>"User not logged in"
]);
exit;
}

$user_id = $_SESSION['user_id'];

$title = $_POST['title'] ?? '';
$author = $_POST['author'] ?? '';
$genre = $_POST['genre'] ?? '';
$description = $_POST['description'] ?? '';
$condition = $_POST['book_condition'] ?? 'Good';
$missing_pages = $_POST['missing_pages'] ?? 'No';
$highlighted = $_POST['highlighted'] ?? 'No';
$rating = $_POST['rating'] ?? 5;
$price = $_POST['price'] ?? 0;

$imageName = '';

if(isset($_FILES['image']) && $_FILES['image']['error']==0){

$targetDir = "../uploads/";

if(!file_exists($targetDir)){
mkdir($targetDir,0777,true);
}

$imageName = time()."_".basename($_FILES["image"]["name"]);
$targetFile = $targetDir.$imageName;

move_uploaded_file($_FILES["image"]["tmp_name"],$targetFile);

}

$stmt = $conn->prepare("
INSERT INTO books 
(user_id,title,author,genre,description,image,book_condition,missing_pages,highlighted,rating,price)
VALUES (?,?,?,?,?,?,?,?,?,?,?)
");

$stmt->bind_param(
"issssssssdi",
$user_id,
$title,
$author,
$genre,
$description,
$imageName,
$condition,
$missing_pages,
$highlighted,
$rating,
$price
);

if($stmt->execute()){

echo json_encode([
"status"=>"success",
"message"=>"Book added successfully!"
]);

}else{

echo json_encode([
"status"=>"error",
"message"=>"Database error"
]);

}

$stmt->close();
$conn->close();
?>