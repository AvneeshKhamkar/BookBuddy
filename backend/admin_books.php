<?php
include 'db_connect.php';
$result = $conn->query("SELECT b.id, b.title, b.author, u.name AS owner FROM books b JOIN users u ON b.user_id = u.id ORDER BY b.id DESC");
echo "<h4>📚 All Books</h4><table class='table table-bordered'><tr><th>ID</th><th>Title</th><th>Author</th><th>Owner</th></tr>";
while ($row = $result->fetch_assoc()) {
  echo "<tr><td>{$row['id']}</td><td>{$row['title']}</td><td>{$row['author']}</td><td>{$row['owner']}</td></tr>";
}
echo "</table>";
?>
