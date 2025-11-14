<?php
header("Content-Type: application/json");
include 'db_connect.php';

$response = [];

try {
    // ✅ Check if table exists before querying
    $checkTable = $conn->query("SHOW TABLES LIKE 'books'");
    if ($checkTable->num_rows === 0) {
        echo json_encode([
            "status" => "error",
            "message" => "Table 'books' does not exist in the database."
        ]);
        exit;
    }

    // ✅ Query to fetch all books along with uploader info
    $sql = "
        SELECT 
            b.id, 
            b.title, 
            b.author, 
            b.genre, 
            b.description, 
            b.image,
            u.name AS owner_name,
            u.email AS owner_email,
            u.id AS user_id
        FROM books b
        JOIN users u ON b.user_id = u.id
        ORDER BY b.id DESC
    ";

    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception("Database query failed: " . $conn->error);
    }

    // ✅ Build the books array
    $books = [];
    while ($row = $result->fetch_assoc()) {
        // Handle missing image gracefully
        $row['image'] = !empty($row['image']) ? $row['image'] : 'default.jpg';
        $books[] = $row;
    }

    // ✅ Final JSON response
    $response = [
        "status" => "success",
        "count"  => count($books),
        "books"  => $books
    ];

} catch (Exception $e) {
    $response = [
        "status" => "error",
        "message" => "Server exception: " . $e->getMessage()
    ];
}

echo json_encode($response, JSON_PRETTY_PRINT);
$conn->close();
?>
