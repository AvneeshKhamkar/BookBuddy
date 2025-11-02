<?php
session_start();
include 'db_connect.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ Fetch all books
$query = "SELECT id, user_id, title, author, genre, description, image FROM books ORDER BY id DESC";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $books = [];
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
    echo json_encode(["status" => "success", "books" => $books]);
} else {
    echo json_encode(["status" => "error", "message" => "No books found"]);
}

$conn->close();
?>
