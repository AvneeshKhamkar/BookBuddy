<?php
session_start();
if(!isset($_SESSION['user_id'])){ header("Location: login.html"); exit; }
$user_name = $_SESSION['user_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Notifications | BookBuddy</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <style>
    body { background: #f6f8fb; font-family: 'Segoe UI', sans-serif; }
    .box {
      max-width: 900px;
      margin: 40px auto;
      background: white;
      border-radius: 18px;
      padding: 22px;
      box-shadow: 0 10px 28px rgba(0,0,0,0.08);
    }
    .notif {
      padding: 14px 16px;
      border-radius: 14px;
      border: 1px solid rgba(0,0,0,0.06);
      margin-bottom: 12px;
    }
    .unread { background: #eef6ff; border-color: rgba(9,105,218,0.2); }
    .small-muted { font-size: 13px; color: #6c757d; }
  </style>
</head>

<body>
  <div class="box">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
      <div>
        <h3 class="mb-0">🔔 Notifications</h3>
        <div class="small-muted">Hello <?php echo htmlspecialchars($user_name); ?>, here are your alerts</div>
      </div>
      <div class="d-flex gap-2">
        <a href="dashboard.php" class="btn btn-secondary">⬅ Dashboard</a>
        <button class="btn btn-primary" id="markReadBtn">✅ Mark all read</button>
      </div>
    </div>

    <div id="notifList">Loading...</div>
  </div>

<script>
function loadNotifications(){
  $.get("../backend/notifications.php?action=list", function(res){
    if(typeof res === "string"){ res = JSON.parse(res); }

    if(res.status !== "success"){
      $("#notifList").html("<p class='text-danger'>Error loading notifications</p>");
      return;
    }

    if(res.notifications.length === 0){
      $("#notifList").html("<p class='text-muted'>No notifications yet.</p>");
      return;
    }

    let html = "";
    res.notifications.forEach(n => {
      html += `
        <div class="notif ${n.is_read == 0 ? "unread" : ""}">
          <div class="fw-bold">${n.message}</div>
          <div class="small-muted">${n.created_at}</div>
        </div>
      `;
    });

    $("#notifList").html(html);
  });
}

$("#markReadBtn").on("click", function(){
  $.post("../backend/notifications.php", {action:"mark_all_read"}, function(res){
    if(typeof res === "string"){ res = JSON.parse(res); }
    alert(res.message);
    loadNotifications();
  });
});

$(document).ready(function(){
  loadNotifications();
});
</script>
</body>
</html>
