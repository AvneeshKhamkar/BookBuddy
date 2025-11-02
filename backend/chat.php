if($action=='send'){
  $msg=$_POST['message'];
  $recv=$_POST['receiver_id'];
  $conn->query("INSERT INTO messages (sender_id,receiver_id,message) VALUES ($user_id,$recv,'$msg')");
  echo json_encode(["status"=>"sent"]); exit();
}
if($action=='fetch'){
  $recv=$_POST['receiver_id'];
  $res=$conn->query("SELECT m.*, u.name as sender_name FROM messages m JOIN users u ON m.sender_id=u.id 
    WHERE (sender_id=$user_id AND receiver_id=$recv) OR (sender_id=$recv AND receiver_id=$user_id)
    ORDER BY m.id ASC");
  $msgs=[]; while($r=$res->fetch_assoc()) $msgs[]=$r;
  echo json_encode($msgs); exit();
}
