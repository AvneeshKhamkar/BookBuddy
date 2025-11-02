<?php
session_start();
header('Content-Type: application/json');

include 'db.php'; // Make sure this connects correctly

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $stmt = $conn->prepare("SELECT id, title, author, genre, image FROM books WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $books = [];
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }

    echo json_encode(["status" => "success", "books" => $books]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Server exception: " . $e->getMessage()]);
}
?>
