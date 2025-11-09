<?php
include 'db_connect.php';
$result = $conn->query("SELECT id, name, email, created_at FROM users ORDER BY id DESC");
echo "<h4>👥 Registered Users</h4><table class='table table-bordered'><tr><th>ID</th><th>Name</th><th>Email</th><th>Created</th></tr>";
while ($row = $result->fetch_assoc()) {
  echo "<tr><td>{$row['id']}</td><td>{$row['name']}</td><td>{$row['email']}</td><td>{$row['created_at']}</td></tr>";
}
echo "</table>";
?>
