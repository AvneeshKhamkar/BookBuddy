<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bookbuddy";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die(json_encode(["status" => "error", "message" => "Database connection failed."]));
}
?>
