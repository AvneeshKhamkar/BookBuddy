<?php
include 'db_connect.php';
header('Content-Type: application/json');

try {
    $users = $conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'];
    $books = $conn->query("SELECT COUNT(*) AS c FROM books")->fetch_assoc()['c'];
    $trades = $conn->query("SELECT COUNT(*) AS c FROM trades")->fetch_assoc()['c'];

    echo json_encode([
        "status" => "success",
        "users" => $users,
        "books" => $books,
        "trades" => $trades
    ]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
