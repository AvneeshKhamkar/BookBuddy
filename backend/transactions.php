$res = $conn->query("SELECT t.*, b.title, 
  s.name AS sender_name, r.name AS receiver_name 
  FROM trade_requests t
  JOIN books b ON t.book_id=b.id
  JOIN users s ON t.sender_id=s.id
  JOIN users r ON t.receiver_id=r.id
  WHERE t.sender_id=$user_id OR t.receiver_id=$user_id");
