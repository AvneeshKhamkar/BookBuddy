<?php
session_start();
include "../backend/db_connect.php";

if(!isset($_SESSION['user_id'])){
header("Location: login.html");
exit;
}

$user_id = $_SESSION['user_id'];

$query = "
SELECT cart.id, cart.quantity, books.id AS book_id,
books.title, books.author, books.image, books.price
FROM cart
JOIN books ON cart.book_id = books.id
WHERE cart.user_id = $user_id
";

$result = $conn->query($query);

$total = 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<title>Shopping Cart | BookBuddy</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<style>

body{
background:linear-gradient(135deg,#fce2db,#e7f0ff,#e5f8f6,#fde6f2);
font-family:'Segoe UI';
}

.cart-container{
max-width:950px;
margin:auto;
}

.cart-card{
background:white;
border-radius:15px;
padding:15px;
box-shadow:0 5px 15px rgba(0,0,0,0.1);
margin-bottom:15px;
display:flex;
align-items:center;
justify-content:space-between;
}

.book-img{
height:100px;
width:80px;
object-fit:cover;
border-radius:8px;
}

.total-box{
background:white;
padding:20px;
border-radius:12px;
box-shadow:0 5px 15px rgba(0,0,0,0.1);
}

.empty-box{
background:white;
padding:40px;
border-radius:12px;
text-align:center;
box-shadow:0 5px 15px rgba(0,0,0,0.1);
}

</style>

</head>

<body>

<div class="container mt-5 cart-container">

<h2 class="mb-4">🛒 My Shopping Cart</h2>

<?php

if($result->num_rows > 0){

while($row = $result->fetch_assoc()){

$item_total = $row['price'] * $row['quantity'];
$total += $item_total;

?>

<div class="cart-card">

<div class="d-flex align-items-center">

<img src="../uploads/<?php echo $row['image']; ?>" class="book-img me-3">

<div>

<h5><?php echo $row['title']; ?></h5>
<p class="text-muted mb-0"><?php echo $row['author']; ?></p>

</div>

</div>

<div>

<input type="number"
value="<?php echo $row['quantity']; ?>"
min="1"
class="form-control"
style="width:80px"
onchange="updateQuantity(<?php echo $row['id']; ?>, this.value)">

</div>

<div>

<strong>₹<?php echo $row['price']; ?></strong>

</div>

<div>

<strong>₹<?php echo $item_total; ?></strong>

</div>

<div>

<button class="btn btn-danger btn-sm"
onclick="removeItem(<?php echo $row['id']; ?>)">
Remove
</button>

</div>

</div>

<?php

}

?>

<div class="total-box mt-4">

<h4>Total Price: ₹<?php echo $total; ?></h4>

<button class="btn btn-danger mt-2" onclick="clearCart()">
Empty Cart
</button>

<a href="checkout.php" class="btn btn-success mt-2">
Proceed to Payment
</a>

<a href="dashboard.php" class="btn btn-secondary mt-2">
Continue Browsing
</a>

</div>

<?php

}else{

?>

<div class="empty-box">

<h4>Your cart is empty</h4>

<p class="text-muted">
Browse books and add them to your cart
</p>

<a href="dashboard.php" class="btn btn-primary">
View Books
</a>

</div>

<?php

}

?>

</div>

<script>

function removeItem(cartId){

$.ajax({

url:"../backend/remove_cart.php",
type:"POST",
data:{cart_id:cartId},
dataType:"json",

success:function(res){

alert(res.message);
location.reload();

}

});

}

function clearCart(){

$.ajax({

url:"../backend/clear_cart.php",
type:"POST",
dataType:"json",

success:function(res){

alert(res.message);
location.reload();

}

});

}

function updateQuantity(cartId, quantity){

$.ajax({

url:"../backend/update_cart.php",
type:"POST",
data:{cart_id:cartId, quantity:quantity},
dataType:"json",

success:function(res){

if(res.status==="success"){
location.reload();
}

}

});

}

</script>

</body>
</html>