<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: login.html");
  exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'] ?? "N/A";
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<title>BookBuddy Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<style>

body{
background:linear-gradient(135deg,#fce2db,#e7f0ff,#e5f8f6,#fde6f2);
font-family:'Segoe UI';
}

.app{
display:flex;
}

/* Sidebar */

.sidebar{
width:230px;
height:100vh;
background:white;
padding:20px;
box-shadow:0 5px 20px rgba(0,0,0,0.1);
position:fixed;
}

.sidebar h4{
margin-bottom:20px;
}

.sidebar a{
display:block;
padding:10px;
margin-bottom:10px;
text-decoration:none;
color:#333;
border-radius:8px;
}

.sidebar a:hover{
background:#f3f3f3;
}

/* Main */

.main{
margin-left:250px;
padding:30px;
width:100%;
}

/* Book Card */

.book-card{
background:white;
border-radius:15px;
padding:15px;
box-shadow:0 5px 20px rgba(0,0,0,0.1);
transition:0.3s;
}

.book-card:hover{
transform:translateY(-5px);
}

.book-cover{
height:200px;
width:100%;
object-fit:cover;
border-radius:10px;
}

.section-title{
margin-top:30px;
margin-bottom:15px;
font-weight:bold;
}

.review-box{
background:#fafafa;
border-radius:10px;
padding:10px;
margin-top:10px;
}

</style>

</head>

<body>

<div class="app">

<!-- Sidebar -->

<div class="sidebar">

<h4>📚 BookBuddy</h4>

<a href="add_book.html">➕ Add Book</a>
<a href="wishlist.php">❤️ Wishlist</a>
<a href="cart.php">🛒 Cart</a>
<a href="trade_history.php">🔄 Trade History</a>
<a href="../backend/logout.php">🚪 Logout</a>

</div>


<!-- Main -->

<div class="main">

<h3>👋 Welcome <?php echo $user_name; ?></h3>
<p><?php echo $user_email; ?></p>

<input type="text" id="searchText" class="form-control mb-4" placeholder="Search books...">


<h4 class="section-title">📕 My Books</h4>
<div class="row" id="myBooksSection"></div>

<h4 class="section-title">📚 Books from Other Users</h4>
<div class="row" id="otherBooksSection"></div>

</div>

</div>

<script>

$(document).ready(function(){

loadBooks();

$("#searchText").on("keyup",function(){
loadBooks();
});

});

function loadBooks(){

$.ajax({

url:"../backend/fetch_books.php",
type:"GET",
dataType:"json",

success:function(res){

if(res.status!="success"){
alert("Error loading books");
return;
}

let myBooksHTML="";
let otherBooksHTML="";

res.books.forEach(book=>{

let isMyBook = book.user_id == <?php echo $user_id; ?>;

let img = book.image
? "../uploads/"+book.image
: "https://via.placeholder.com/300x200";

let card=`

<div class="col-md-4 mb-4">

<div class="book-card">

<img src="${img}" class="book-cover">

<h5 class="mt-2">${book.title}</h5>

<p><b>Author:</b> ${book.author}</p>
<p><b>Genre:</b> ${book.genre || "N/A"}</p>
<p><b>Price:</b> ₹${book.price || 0}</p>

<div class="mb-2">

${
isMyBook
?
`<button class="btn btn-danger btn-sm"
onclick="deleteBook(${book.id})">
Delete
</button>`
:
`
<button class="btn btn-success btn-sm"
onclick="requestTrade(${book.id},${book.user_id})">
Trade
</button>

<button class="btn btn-warning btn-sm"
onclick="addToCart(${book.id})">
Cart
</button>

<button class="btn btn-outline-danger btn-sm"
onclick="toggleWishlist(${book.id})">
Wishlist
</button>
`
}

</div>

${
!isMyBook
?
`
<div class="review-box">

<select class="form-control mb-2" id="rating_${book.id}">
<option value="">Rating</option>
<option value="5">⭐⭐⭐⭐⭐</option>
<option value="4">⭐⭐⭐⭐</option>
<option value="3">⭐⭐⭐</option>
<option value="2">⭐⭐</option>
<option value="1">⭐</option>
</select>

<textarea class="form-control mb-2"
id="review_${book.id}"
placeholder="Write review"></textarea>

<button class="btn btn-primary btn-sm"
onclick="submitReview(${book.id})">
Submit Review
</button>

<div id="reviews_${book.id}" class="mt-2"></div>

</div>
`
:""
}

</div>

</div>
`;

if(isMyBook){
myBooksHTML += card;
}
else{
otherBooksHTML += card;
}

});

$("#myBooksSection").html(myBooksHTML || "<p>No books uploaded yet.</p>");
$("#otherBooksSection").html(otherBooksHTML || "<p>No books available.</p>");

res.books.forEach(book=>{
if(book.user_id != <?php echo $user_id; ?>){
loadReviews(book.id);
}
});

}

});

}


/* Delete */

function deleteBook(bookId){

if(!confirm("Delete this book?")) return;

$.ajax({

url:"../backend/delete_book.php",
type:"POST",
data:{book_id:bookId},
dataType:"json",

success:function(res){
alert(res.message);
loadBooks();
}

});

}


/* Trade */

function requestTrade(bookId,ownerId){

$.ajax({

url:"../backend/trade.php",
type:"POST",
data:{book_id:bookId,owner_id:ownerId},
dataType:"json",

success:function(res){
alert(res.message);
}

});

}


/* Cart */

function addToCart(bookId){

$.ajax({

url:"../backend/add_to_cart.php",
type:"POST",
data:{book_id:bookId},
dataType:"json",

success:function(res){
alert(res.message);
}

});

}


/* Wishlist */

function toggleWishlist(bookId){

$.ajax({

url:"../backend/wishlist.php",
type:"POST",
data:{action:"toggle",book_id:bookId},
dataType:"json",

success:function(res){
alert(res.message);
}

});

}


/* Reviews */

function submitReview(bookId){

let rating=$("#rating_"+bookId).val();
let review=$("#review_"+bookId).val();

if(!rating){
alert("Please select rating");
return;
}

$.ajax({

url:"../backend/add_review.php",
type:"POST",
data:{book_id:bookId,rating:rating,review:review},
dataType:"json",

success:function(res){

alert(res.message);

if(res.status==="success"){
loadReviews(bookId);
}

}

});

}

function loadReviews(bookId){

$.ajax({

url:"../backend/fetch_reviews.php",
type:"GET",
data:{book_id:bookId},
dataType:"json",

success:function(res){

if(res.status==="success"){

let html="";

res.reviews.forEach(r=>{

html+=`
<div class="card p-2 mb-1">
<b>${r.name}</b><br>
⭐ ${r.rating}/5<br>
${r.review}
</div>
`;

});

$("#reviews_"+bookId).html(html);

}

}

});

}

</script>

</body>
</html>