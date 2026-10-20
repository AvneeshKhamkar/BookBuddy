<?php
session_start();
include "db_connect.php";
header("Content-Type: application/json");
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
if(!$name || !$email || !$password){
   echo json_encode(["status"=>"error","message"=>"All fields required"]);
   exit;
}
// email validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
   echo json_encode(["status"=>"error","message"=>"Invalid email format"]);
   exit;
}
// password length
if (strlen($password) < 6) {
   echo json_encode(["status"=>"error","message"=>"Password must be at least 6 characters"]);
   exit;
}
// check existing
$check = $conn->prepare("SELECT id FROM users WHERE email=?");
$check->bind_param("s",$email);
$check->execute();
$res = $check->get_result();
if($res->num_rows > 0){
   echo json_encode(["status"=>"error","message"=>"Email already exists"]);
   exit;
}
$hashed = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users (name,email,password) VALUES (?,?,?)");
$stmt->bind_param("sss",$name,$email,$hashed);
if($stmt->execute()){
   echo json_encode(["status"=>"success","message"=>"Registered successfully"]);
}else{
   echo json_encode(["status"=>"error","message"=>"Database error"]);
}
?>