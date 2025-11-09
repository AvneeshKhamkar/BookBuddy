<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
  header("Location: admin_login.html");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>BookBuddy | Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <style>
    body {
      background-color: #f3f4f6;
      font-family: 'Poppins', sans-serif;
    }
    .sidebar {
      width: 250px;
      height: 100vh;
      position: fixed;
      top: 0; left: 0;
      background: #1f2937;
      color: white;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 20px 0;
    }
    .sidebar h3 {
      text-align: center;
      font-weight: 600;
      color: #facc15;
    }
    .nav-item {
      padding: 12px 25px;
      cursor: pointer;
      color: #d1d5db;
      transition: all 0.3s;
      font-weight: 500;
    }
    .nav-item:hover, .nav-item.active {
      background: #374151;
      color: #fff;
    }
    .content {
      margin-left: 260px;
      padding: 30px;
    }
    .card {
      border-radius: 15px;
      box-shadow: 0 3px 6px rgba(0,0,0,0.1);
      background: #fff;
      padding: 20px;
    }
    .topbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 25px;
    }
    .logout-btn {
      background: #dc2626;
      border: none;
      color: white;
      padding: 8px 15px;
      border-radius: 6px;
    }
    .logout-btn:hover {
      background: #b91c1c;
    }
  </style>
</head>

<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <div>
      <h3>📚 BookBuddy Admin</h3>
      <div class="mt-4">
        <div class="nav-item active" id="dashboard"><i class="bi bi-speedometer2 me-2"></i> Dashboard</div>
        <div class="nav-item" id="users"><i class="bi bi-people me-2"></i> Manage Users</div>
        <div class="nav-item" id="books"><i class="bi bi-journal-bookmark me-2"></i> Manage Books</div>
        <div class="nav-item" id="trades"><i class="bi bi-arrow-repeat me-2"></i> Manage Trades</div>
      </div>
    </div>
    <div class="text-center mb-3">
      <form action="../backend/logout.php" method="POST">
        <button class="logout-btn" type="submit"><i class="bi bi-box-arrow-right me-1"></i> Logout</button>
      </form>
    </div>
  </div>

  <!-- Main Content -->
  <div class="content">
    <div class="topbar">
      <h2 class="fw-semibold"><i class="bi bi-speedometer2 me-2"></i>Admin Dashboard</h2>
      <h5 class="text-muted mb-0">Welcome, <?php echo $_SESSION['admin_username']; ?> 👋</h5>
    </div>

    <div id="adminContent">
      <div class="row text-center">
        <div class="col-md-4">
          <div class="card">
            <h5>👥 Total Users</h5>
            <h3 id="userCount">-</h3>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card">
            <h5>📚 Total Books</h5>
            <h3 id="bookCount">-</h3>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card">
            <h5>🔄 Total Trades</h5>
            <h3 id="tradeCount">-</h3>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Load summary stats
    $(document).ready(function() {
      loadStats();
    });

    function loadStats() {
      $.get("../backend/admin_stats.php", function(data) {
        if (data.status === "success") {
          $("#userCount").text(data.users);
          $("#bookCount").text(data.books);
          $("#tradeCount").text(data.trades);
        }
      }, "json");
    }

    // Navigation button actions
    $(".nav-item").click(function() {
      $(".nav-item").removeClass("active");
      $(this).addClass("active");

      let id = $(this).attr("id");
      let url = "";

      if (id === "users") url = "../backend/admin_users.php";
      else if (id === "books") url = "../backend/admin_books.php";
      else if (id === "trades") url = "../backend/admin_trades.php";
      else {
        $("#adminContent").html(`<div class="text-center mt-5"><h4>Welcome back, Admin!</h4><p>Choose a section to manage.</p></div>`);
        return;
      }

      $("#adminContent").html("<p class='text-center mt-5 text-muted'>Loading...</p>");
      $.get(url, function(data) {
        $("#adminContent").html(data);
      });
    });
  </script>
</body>
</html>
