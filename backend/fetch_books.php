<?php

header("Content-Type: application/json");

include 'db_connect.php';

$response = [];

try {

    $search = $_GET['search'] ?? '';

    $genre = $_GET['genre'] ?? '';

    $condition = $_GET['condition'] ?? '';

    // ✅ Base query

    $sql = "

        SELECT 

            b.id,

            b.title,

            b.author,

            b.genre,

            b.description,

            b.image,

            b.price,

            b.book_condition,

            b.missing_pages,

            b.rating,

            u.name AS owner_name,

            u.email AS owner_email,
u.id AS user_id

        FROM books b

        JOIN users u ON b.user_id = u.id

        WHERE 1=1

    ";

    // ✅ Apply filters safely

    if (!empty($search)) {

        $search = $conn->real_escape_string($search);

        $sql .= " AND (

            b.title LIKE '%$search%' OR

            b.author LIKE '%$search%' OR

            b.genre LIKE '%$search%'

        )";

    }

    if (!empty($genre)) {

        $genre = $conn->real_escape_string($genre);

        $sql .= " AND b.genre LIKE '%$genre%'";

    }

    if (!empty($condition)) {

        $condition = $conn->real_escape_string($condition);

        $sql .= " AND b.book_condition = '$condition'";

    }

    // ✅ Order

    $sql .= " ORDER BY b.id DESC";

    $result = $conn->query($sql);

    if (!$result) {

        throw new Exception($conn->error);

    }

    $books = [];

    while ($row = $result->fetch_assoc()) {

        $books[] = $row;

    }

    echo json_encode([

        "status" => "success",

        "books" => $books

    ]);

} catch (Exception $e) {

    echo json_encode([

        "status" => "error",

        "message" => "Server exception: " . $e->getMessage()

    ]);

}

$conn->close();

?>
 