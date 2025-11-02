<?php
session_start();
include 'db_connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $sql = "
        SELECT 
            t.id, t.book_id, t.requester_id, t.owner_id, t.status,
            t.trade_type, t.offered_book_id, t.offered_price, t.created_at,
            b.title AS book_title,
            u1.name AS requester_name,
            u2.name AS owner_name,
            ob.title AS offered_book_title
        FROM trades t
        JOIN books b ON t.book_id = b.id
        JOIN users u1 ON t.requester_id = u1.id
        JOIN users u2 ON t.owner_id = u2.id
        LEFT JOIN books ob ON t.offered_book_id = ob.id
        WHERE t.requester_id = ? OR t.owner_id = ?
        ORDER BY t.created_at DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $trades = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode(["status" => "success", "trades" => $trades]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Server exception: " . $e->getMessage()]);
}
