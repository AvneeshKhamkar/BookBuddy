<?php
session_start();
include 'db_connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit;
}

$trade_id = $_POST['trade_id'] ?? null;
$status = $_POST['status'] ?? '';
$trade_type = $_POST['trade_type'] ?? null;
$offered_book_id = $_POST['offered_book_id'] ?? null;
$offered_price = $_POST['offered_price'] ?? null;

if (!$trade_id) {
    echo json_encode(["status" => "error", "message" => "Missing trade ID."]);
    exit;
}

if (!in_array($status, ['accepted', 'declined', 'pending'])) {
    echo json_encode(["status" => "error", "message" => "Invalid status value."]);
    exit;
}

if ($status === 'accepted' && $trade_type) {
    // Update with type and additional info
    $stmt = $conn->prepare("UPDATE trades SET status=?, trade_type=?, offered_book_id=?, offered_price=? WHERE id=?");
    $stmt->bind_param("ssddi", $status, $trade_type, $offered_book_id, $offered_price, $trade_id);
} else {
    // Basic accept/decline
    $stmt = $conn->prepare("UPDATE trades SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $trade_id);
}

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Trade updated successfully!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Database error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
