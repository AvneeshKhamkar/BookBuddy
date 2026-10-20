<?php
session_start();
include "db_connect.php";
header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["status"=>"error","message"=>"User not logged in"]);
    exit;
}

$user_id = $_SESSION["user_id"];
$action = $_GET["action"] ?? $_POST["action"] ?? "list";

// ✅ List notifications
if ($action === "list") {
    $stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();

    $notifs = [];
    while ($row = $res->fetch_assoc()) $notifs[] = $row;

    echo json_encode(["status"=>"success","notifications"=>$notifs]);
    exit;
}

// ✅ Unread count
if ($action === "count") {
    $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();

    echo json_encode(["status"=>"success","unread"=>$res["cnt"]]);
    exit;
}

// ✅ Mark all read
if ($action === "mark_all_read") {
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        echo json_encode(["status"=>"success","message"=>"All notifications marked as read"]);
    } else {
        echo json_encode(["status"=>"error","message"=>"Database error"]);
    }
    exit;
}

echo json_encode(["status"=>"error","message"=>"Invalid action"]);
$conn->close();
?>
