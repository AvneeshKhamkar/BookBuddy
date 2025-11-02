<?php
include("db.php");

if (isset($conn) && $conn instanceof mysqli && $conn->connect_errno === 0) {
    echo "✅ Database connected successfully!";
} else {
    echo "❌ Database connection failed!";
}
?>
