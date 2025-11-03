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
            t.id, 
            t.status, 
            t.trade_type,
            t.offered_book_id,
            t.offered_price,
            t.created_at,
            b.title AS book_title,
            requester.name AS requester_name,
            requester.email AS requester_email,
            owner.name AS owner_name,
            owner.email AS owner_email,
            ob.title AS offered_book_title
        FROM trades t
        JOIN books b ON t.book_id = b.id
        JOIN users requester ON t.requester_id = requester.id
        JOIN users owner ON t.owner_id = owner.id
        LEFT JOIN books ob ON t.offered_book_id = ob.id
        WHERE t.requester_id = ? OR t.owner_id = ?
        ORDER BY t.created_at DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $trades = [];
    while ($row = $result->fetch_assoc()) {
        $trades[] = $row;
    }

    echo json_encode(["status" => "success", "trades" => $trades]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Server exception: " . $e->getMessage()]);
}
?>
