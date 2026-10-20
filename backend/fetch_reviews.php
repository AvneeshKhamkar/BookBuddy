<?php

include "db_connect.php";

$book_id = $_GET['book_id'];

$query = "

SELECT reviews.rating,
reviews.review,
users.name

FROM reviews

JOIN users
ON reviews.user_id = users.id

WHERE reviews.book_id = $book_id

ORDER BY reviews.id DESC

";

$result = $conn->query($query);

$reviews=[];

while($row=$result->fetch_assoc()){

$reviews[]=$row;

}

echo json_encode([
"status"=>"success",
"reviews"=>$reviews
]);

?>