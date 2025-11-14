<?php
include 'db_connect.php';
header('Content-Type: application/json');

try {
    $where = [];
    $params = [];
    $types = '';

    // ✅ Handle Filters
    if (!empty($_GET['user'])) {
        $where[] = "(r.name LIKE ? OR o.name LIKE ?)";
        $params[] = "%" . $_GET['user'] . "%";
        $params[] = "%" . $_GET['user'] . "%";
        $types .= 'ss';
    }

    if (!empty($_GET['status'])) {
        $where[] = "t.status = ?";
        $params[] = $_GET['status'];
        $types .= 's';
    }

    // ✅ Main Query
    $sql = "SELECT 
                t.id, 
                b.title AS book_title,
                r.name AS requester_name, 
                o.name AS owner_name,
                t.status, 
                t.created_at
            FROM trades t
            JOIN books b ON t.book_id = b.id
            JOIN users r ON t.requester_id = r.id
            JOIN users o ON t.owner_id = o.id";

    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }

    $sql .= " ORDER BY t.created_at DESC";

    $stmt = $conn->prepare($sql);

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $trades = [];
    while ($row = $result->fetch_assoc()) {
        $trades[] = $row;
    }

    echo json_encode([
        "status" => "success",
        "trades" => $trades
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Server exception: " . $e->getMessage()
    ]);
}

$conn->close();
?>
