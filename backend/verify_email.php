<?php

include "db_connect.php";

if (isset($_GET['token'])) {

    $token = $_GET['token'];

    $stmt = $conn->prepare("UPDATE users SET is_verified=1 WHERE verification_token=?");

    $stmt->bind_param("s", $token);

    if ($stmt->execute()) {

        echo "<h2>✅ Email Verified Successfully!</h2>";

    } else {

        echo "<h2>❌ Verification Failed</h2>";

    }

}

?>
 