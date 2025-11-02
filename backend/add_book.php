<?php
session_start();
include 'db_connect.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$title = $_POST['title'] ?? '';
$author = $_POST['author'] ?? '';
$genre = $_POST['genre'] ?? '';
$description = $_POST['description'] ?? '';
$imageName = '';

if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $targetDir = "../uploads/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    $imageName = time() . "_" . basename($_FILES["image"]["name"]);
    $targetFile = $targetDir . $imageName;

    if (!move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
        echo json_encode(["status" => "error", "message" => "Failed to upload image"]);
        exit;
    }
}

// Prepare SQL (✅ use `image` column instead of `image_path`)
$stmt = $conn->prepare("INSERT INTO books (user_id, title, author, genre, description, image) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssss", $user_id, $title, $author, $genre, $description, $imageName);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Book added successfully!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Database error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
