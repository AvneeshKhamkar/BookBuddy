<?php
header('Content-Type: application/json');
include('db_connect.php');
session_start();

if (!isset($_POST['book_id']) || !isset($_POST['target_user_id'])) {
    echo json_encode(['status'=>'error','message'=>'Missing parameters']);
    exit;
}

$book_id = $_POST['book_id'];
$target_user_id = $_POST['target_user_id'];
$user_id = $_SESSION['user_id'] ?? 1; // for testing

$stmt = $conn->prepare("INSERT INTO trades (book_id, from_user, to_user, trade_date) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("iii", $book_id, $user_id, $target_user_id);

if ($stmt->execute()) {
    echo json_encode(['status'=>'success','message'=>'Trade recorded']);
} else {
    echo json_encode(['status'=>'error','message'=>$conn->error]);
}
?>
