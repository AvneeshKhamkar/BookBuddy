<?php
session_start();
include "../backend/db_connect.php";

$user_id = $_SESSION['user_id'];

$query="
SELECT books.title, books.price, cart.quantity
FROM cart
JOIN books ON cart.book_id = books.id
WHERE cart.user_id=$user_id
";

$result=$conn->query($query);

$total=0;

while($row = $result->fetch_assoc()){
    $item_total = $row['price'] * $row['quantity'];
    $total += $item_total; 
}

$_SESSION['payment_amount'] = $total;


?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<title>Checkout | BookBuddy</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<style>
body{
background:linear-gradient(135deg,#fce2db,#e7f0ff,#e5f8f6,#fde6f2);
font-family:'Segoe UI';
}

.card{
border-radius:15px;
box-shadow:0 8px 25px rgba(0,0,0,0.1);
}
</style>

</head>

<body>

<div class="container mt-5">

<div class="card p-4">

<h3>🛒 Order Summary</h3>

<?php
while($row=$result->fetch_assoc()){
$item_total=$row['price']*$row['quantity'];
$total+=$item_total;
?>

<div class="d-flex justify-content-between">
<div>
<strong><?php echo $row['title']; ?></strong><br>
<small>Qty: <?php echo $row['quantity']; ?></small>
</div>
<div>₹<?php echo $item_total; ?></div>
</div>
<hr>

<?php } ?>

<h4>Total: ₹<?php echo $total; ?></h4>

<!-- ADDRESS FORM -->

<h5 class="mt-4">📍 Delivery Address</h5>

<form id="addressForm">

<input type="text" name="full_name" class="form-control mb-2" placeholder="Full Name" required>

<input type="text" name="phone" class="form-control mb-2" placeholder="Phone Number" required>

<textarea name="address" class="form-control mb-2" placeholder="Full Address" required></textarea>

<input type="text" name="city" class="form-control mb-2" placeholder="City" required>

<input type="text" name="pincode" class="form-control mb-2" placeholder="Pincode" required>

</form>

<button class="btn btn-success w-100 mt-3" id="payBtn">
Pay with Razorpay
</button>

</div>

</div>

<script>

var totalAmount = <?php echo $total * 100; ?>;

var rzp = new Razorpay({
key: "rzp_test_SXshrwfqD2a5c3",
amount: totalAmount,
currency: "INR",
name: "BookBuddy",
description: "Book Purchase",
handler: function (response){

window.location.href="../backend/verify_payment.php?payment_id="+response.razorpay_payment_id;

}
});

$("#payBtn").click(function(e){

e.preventDefault();

$.ajax({

url:"../backend/save_address.php",
type:"POST",
data:$("#addressForm").serialize(),
dataType:"json",

success:function(res){

if(res.status==="success"){
rzp.open();
}else{
alert("Error saving address");
}

}

});

});

</script>

</body>
</html>