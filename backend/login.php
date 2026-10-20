<?php
session_start();
include "db_connect.php";
header("Content-Type: application/json");
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
if (!$email || !$password) {
   echo json_encode(["status"=>"error","message"=>"All fields required"]);
   exit;
}
$stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
   if (password_verify($password, $row['password'])) {
       $_SESSION['user_id'] = $row['id'];
       $_SESSION['user_name'] = $row['name'];
       $_SESSION['user_email'] = $row['email'];
       echo json_encode(["status"=>"success","message"=>"Login successful"]);
   } else {
       echo json_encode(["status"=>"error","message"=>"Wrong password"]);
   }
} else {
   echo json_encode(["status"=>"error","message"=>"User not found"]);
}
$conn->close();
?>