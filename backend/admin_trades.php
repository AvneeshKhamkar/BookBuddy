<?php
include 'db_connect.php';
$result = $conn->query("SELECT t.id, b.title AS book_title, r.name AS requester, o.name AS owner, t.status 
FROM trades t 
JOIN books b ON t.book_id = b.id
JOIN users r ON t.requester_id = r.id
JOIN users o ON t.owner_id = o.id
ORDER BY t.id DESC");
echo "<h4>🔄 Trade Requests</h4><table class='table table-bordered'><tr><th>ID</th><th>Book</th><th>Requester</th><th>Owner</th><th>Status</th></tr>";
while ($row = $result->fetch_assoc()) {
  echo "<tr><td>{$row['id']}</td><td>{$row['book_title']}</td><td>{$row['requester']}</td><td>{$row['owner']}</td><td>{$row['status']}</td></tr>";
}
echo "</table>";
?>
