<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit;
}

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['book_id'])) {
        echo json_encode(["status" => "error", "message" => "Missing book ID."]);
        exit;
    }

    $book_id = intval($_POST['book_id']);
    $user_id = intval($_SESSION['user_id']);

    try {
        // Verify ownership first
        $stmt = $conn->prepare("SELECT image_path FROM books WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $book_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            echo json_encode(["status" => "error", "message" => "Book not found or unauthorized."]);
            exit;
        }

        $book = $result->fetch_assoc();
        $image_path = $book['image_path'];

        // Delete the book record
        $stmt = $conn->prepare("DELETE FROM books WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $book_id, $user_id);

        if ($stmt->execute()) {
            // Delete the image file if exists
            if ($image_path && file_exists($image_path)) {
                unlink($image_path);
            }
            echo json_encode(["status" => "success", "message" => "✅ Book deleted successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Database delete failed."]);
        }
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Server exception: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>
