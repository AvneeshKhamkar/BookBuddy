<?php
session_start();
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$book_id = $_POST['book_id'] ?? '';
$owner_id = $_POST['owner_id'] ?? '';

if (empty($book_id) || empty($owner_id)) {
    echo json_encode(["status" => "error", "message" => "Missing parameters."]);
    exit;
}

// Prevent user from trading their own book
if ($user_id == $owner_id) {
    echo json_encode(["status" => "error", "message" => "You cannot trade your own book."]);
    exit;
}

// Insert new trade request
$stmt = $conn->prepare("INSERT INTO trades (book_id, requester_id, owner_id, status, created_at) VALUES (?, ?, ?, 'pending', NOW())");
$stmt->bind_param("iii", $book_id, $user_id, $owner_id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Trade request sent successfully!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Database error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
