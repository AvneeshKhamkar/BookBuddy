<?php
session_start();
include "../backend/db_connect.php";

$payment_id = $_GET['payment_id'];
$address_id = $_SESSION['last_address_id'];

$query = "SELECT * FROM addresses WHERE id=$address_id";
$result = $conn->query($query);
$addr = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>

<head>
<title>Payment Success</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
background:linear-gradient(135deg,#e0ffe0,#e0f7ff);
font-family:'Segoe UI';
}
.card{
max-width:600px;
margin:auto;
margin-top:80px;
padding:30px;
border-radius:15px;
box-shadow:0 10px 25px rgba(0,0,0,0.1);
}
</style>

</head>

<body>

<div class="card text-center">

<h2>✅ Payment Successful</h2>

<p>Payment ID: <strong><?php echo $payment_id; ?></strong></p>

<hr>

<h5>📍 Delivery Address</h5>

<p>
<?php echo $addr['full_name']; ?><br>
<?php echo $addr['address']; ?><br>
<?php echo $addr['city']; ?> - <?php echo $addr['pincode']; ?><br>
📞 <?php echo $addr['phone']; ?>
</p>

<button onclick="window.print()" class="btn btn-success">
Download Receipt
</button>

<a href="dashboard.php" class="btn btn-primary mt-2">
Back to Dashboard
</a>

</div>

</body>
</html>