<?php
session_start();
include 'db_connect.php';
header('Content-Type: application/json');

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "Please enter both username and password."]);
    exit;
}

// ✅ Check admin by username instead of email
$stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();

    if (password_verify($password, $admin['password'])) {
        // ✅ Set session variables
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['username'];
        $_SESSION['is_admin'] = true;

        echo json_encode([
            "status" => "success",
            "message" => "Welcome, $username!",
            "redirect" => "../admin/admin_dashboard.php"
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "❌ Wrong password."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "❌ Admin not found."]);
}

$stmt->close();
$conn->close();
?>
