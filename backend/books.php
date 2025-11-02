<?php
session_start();
include 'db_connect.php';

header('Content-Type: application/json'); // ✅ Ensure valid JSON output

// Check database connection
if (!$conn) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

$action = $_POST['action'] ?? '';

switch ($action) {

    // 🔹 Add new book
    case 'add_book':
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
            exit;
        }

        $user_id = $_SESSION['user_id'];  // ✅ FIXED: include user ID from session
        $title = $_POST['title'] ?? '';
        $author = $_POST['author'] ?? '';
        $genre = $_POST['genre'] ?? '';
        $description = $_POST['description'] ?? '';

        $image = null;
        if (!empty($_FILES['image']['name'])) {
            $uploadDir = "../uploads/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $image = time() . "_" . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $image);
        }

        $stmt = $conn->prepare("INSERT INTO books (user_id, title, author, genre, description, image_path) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $user_id, $title, $author, $genre, $description, $image);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Book added successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $stmt->error]);
        }
        $stmt->close();
        break;

    // 🔹 List all books
    case 'list_books':
        $books = [];
        $result = $conn->query("SELECT * FROM books ORDER BY id DESC");
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
        echo json_encode(['status' => 'success', 'books' => $books]);
        break;

    // 🔹 Delete book
    case 'delete_book':
        $id = intval($_POST['id'] ?? 0);
        $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        echo json_encode(['status' => $ok ? 'success' : 'error']);
        break;

    // 🔹 Update book
    case 'update_book':
        $id = intval($_POST['id'] ?? 0);
        $title = $_POST['title'] ?? '';
        $author = $_POST['author'] ?? '';
        $genre = $_POST['genre'] ?? '';
        $description = $_POST['description'] ?? '';
        $image = null;

        if (!empty($_FILES['image']['name'])) {
            $uploadDir = "../uploads/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $image = time() . "_" . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $image);

            $stmt = $conn->prepare("UPDATE books SET title=?, author=?, genre=?, description=?, image=? WHERE id=?");
            $stmt->bind_param("sssssi", $title, $author, $genre, $description, $image, $id);
        } else {
            $stmt = $conn->prepare("UPDATE books SET title=?, author=?, genre=?, description=? WHERE id=?");
            $stmt->bind_param("ssssi", $title, $author, $genre, $description, $id);
        }

        $ok = $stmt->execute();
        echo json_encode(['status' => $ok ? 'success' : 'error']);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}
?>
