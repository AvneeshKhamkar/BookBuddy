<?php
header('Content-Type: application/json');
session_start();
require_once 'db.php';

try {
    if (!isset($_POST['email'], $_POST['password'])) {
        echo json_encode(["status" => "error", "message" => "Missing email or password."]);
        exit;
    }

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            echo json_encode([
                "status" => "success",
                "message" => "Login successful!",
                "user_name" => $user['name']
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid password."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "User not found."]);
    }
} catch (Throwable $e) {
    echo json_encode(["status" => "error", "message" => "Server error: " . $e->getMessage()]);
}
