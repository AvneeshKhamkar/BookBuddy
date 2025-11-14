<?php
header("Content-Type: application/json");
include 'db_connect.php';

try {
    // ✅ Check database connection
    if (!$conn) {
        echo json_encode(["status" => "error", "message" => "Database connection failed"]);
        exit;
    }

    // ✅ Query to get all users and count how many books each added
    $sql = "
        SELECT 
            u.id, 
            u.name, 
            u.email,
            COUNT(b.id) AS total_books
        FROM users u
        LEFT JOIN books b ON u.id = b.user_id
        GROUP BY u.id, u.name, u.email
        ORDER BY u.name ASC
    ";

    $result = $conn->query($sql);

    if (!$result) {
        echo json_encode(["status" => "error", "message" => "SQL Error: " . $conn->error]);
        exit;
    }

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    echo json_encode([
        "status" => "success",
        "count" => count($users),
        "users" => $users
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Server Exception: " . $e->getMessage()
    ]);
}

$conn->close();
?>
