<?php
session_start();
include 'db_connect.php';
header('Content-Type: application/json');

// ✅ Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit;
}

// ✅ Check for book_id
if (empty($_POST['book_id'])) {
    echo json_encode(["status" => "error", "message" => "Missing book ID."]);
    exit;
}

$book_id = intval($_POST['book_id']);
$user_id = $_SESSION['user_id'];

// ✅ Delete only books owned by the logged-in user
$stmt = $conn->prepare("DELETE FROM books WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $book_id, $user_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["status" => "success", "message" => "Book deleted successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Book not found or not yours."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Database error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
