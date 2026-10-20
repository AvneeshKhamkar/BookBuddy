<?php
session_start();
include "db_connect.php";
header("Content-Type: application/json");

// ✅ Check login
if (!isset($_SESSION["user_id"])) {
    echo json_encode(["status" => "error", "message" => "User not logged in."]);
    exit;
}

$user_id = $_SESSION["user_id"];
$action = $_POST["action"] ?? $_GET["action"] ?? "";

// ✅ Toggle Wishlist (Add/Remove)
if ($action === "toggle") {

    $book_id = $_POST["book_id"] ?? null;
    if (!$book_id) {
        echo json_encode(["status" => "error", "message" => "Missing book_id"]);
        exit;
    }

    // ✅ Check already exists
    $check = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND book_id = ?");
    $check->bind_param("ii", $user_id, $book_id);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows > 0) {
        // ✅ Remove from wishlist
        $del = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND book_id = ?");
        $del->bind_param("ii", $user_id, $book_id);

        if ($del->execute()) {
            echo json_encode(["status" => "success", "message" => "❌ Removed from wishlist"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Database error removing wishlist"]);
        }
        $del->close();
    } else {
        // ✅ Add to wishlist
        $ins = $conn->prepare("INSERT INTO wishlist (user_id, book_id) VALUES (?, ?)");
        $ins->bind_param("ii", $user_id, $book_id);

        if ($ins->execute()) {
            echo json_encode(["status" => "success", "message" => "✅ Added to wishlist ❤️"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Database error adding wishlist"]);
        }
        $ins->close();
    }

    $check->close();
    $conn->close();
    exit;
}


// ✅ Fetch Wishlist (GET) - optional use for later
if ($action === "list") {
    $sql = "
        SELECT 
            w.id as wishlist_id,
            b.id as book_id,
            b.title,
            b.author,
            b.genre,
            b.image,
            b.book_condition,
            b.rating,
            b.description
        FROM wishlist w
        JOIN books b ON w.book_id = b.id
        WHERE w.user_id = ?
        ORDER BY w.created_at DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $books = [];
    while ($row = $result->fetch_assoc()) {
        $row["image"] = !empty($row["image"]) ? $row["image"] : "default.jpg";
        $books[] = $row;
    }

    echo json_encode(["status" => "success", "wishlist" => $books]);
    $stmt->close();
    $conn->close();
    exit;
}


// ✅ Invalid action
echo json_encode(["status" => "error", "message" => "Invalid action."]);
$conn->close();
?>
