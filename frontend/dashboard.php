<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// Retrieve user info from session
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'] ?? "N/A";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>📚 Dashboard | BookBuddy</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

  <style>
    body {
      background: linear-gradient(120deg, #fdfbfb, #fce4ec);
      font-family: 'Segoe UI', sans-serif;
      color: #2c3e50;
      min-height: 100vh;
      margin: 0;
      display: flex;
    }
    .sidebar {
      width: 220px;
      background: rgba(255, 255, 255, 0.95);
      box-shadow: 2px 0 10px rgba(0,0,0,0.08);
      padding: 20px;
      position: fixed;
      top: 0;
      left: -220px;
      height: 100%;
      transition: left 0.3s ease;
      z-index: 100;
    }
    .sidebar.active { left: 0; }
    .sidebar h4 { margin-bottom: 20px; color: #34495e; }
    .sidebar a {
      display: block;
      color: #444;
      text-decoration: none;
      padding: 10px 0;
      border-radius: 8px;
      transition: background 0.3s;
    }
    .sidebar a:hover { background: rgba(255, 182, 193, 0.2); }
    .menu-btn {
      font-size: 1.8rem;
      background: none;
      border: none;
      cursor: pointer;
      position: fixed;
      top: 20px;
      left: 20px;
      z-index: 101;
      color: #444;
    }
    .content {
      margin-left: 0;
      padding: 60px 30px;
      width: 100%;
      transition: margin-left 0.3s ease;
    }
    .sidebar.active ~ .content { margin-left: 220px; }
    .header-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      margin-bottom: 30px;
    }
    .user-info {
      background: rgba(255, 255, 255, 0.85);
      border-radius: 12px;
      padding: 12px 18px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .search-box { width: 300px; }
    .btn-primary {
      background: linear-gradient(135deg, #a5d8ff, #b9fbc0);
      border: none;
      border-radius: 10px;
      transition: all 0.3s ease;
    }
    .btn-primary:hover {
      background: linear-gradient(135deg, #91c8ff, #aaf1c3);
      box-shadow: 0 4px 15px rgba(160, 220, 255, 0.4);
    }
    .book-card {
      border: none;
      border-radius: 15px;
      padding: 20px;
      background: #ffffff;
      box-shadow: 0 4px 18px rgba(0,0,0,0.08);
      margin-bottom: 25px;
      transition: all 0.3s ease;
    }
    .book-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 22px rgba(0,0,0,0.12);
    }
    .book-card img {
      width: 100%;
      height: 200px;
      object-fit: cover;
      border-radius: 12px;
    }
    .book-actions {
      margin-top: 10px;
      display: flex;
      justify-content: space-between;
    }
    .section-title {
      font-size: 1.3rem;
      font-weight: 600;
      color: #455a64;
      margin-bottom: 15px;
    }
    #myBooksList {
      background: rgba(255, 255, 255, 0.8);
      padding: 20px;
      border-radius: 15px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
  </style>
</head>

<body>
  <!-- Sidebar -->
  <button class="menu-btn" id="menuBtn">☰</button>
  <div class="sidebar" id="sidebar">
    <h4>📚 BookBuddy</h4>
    <a href="add_book.html">➕ Trade</a>
    <a href="trade_history.php">📜 Trade History</a>
    <a href="../backend/logout.php">🚪 Logout</a>
  </div>

  <!-- Main Content -->
  <div class="content">
    <div class="container-fluid">
      <div class="header-bar">
        <div>
          <h2>👋 Welcome, <?php echo htmlspecialchars($user_name); ?></h2>
          <div class="user-info mt-2">
            📧 <?php echo htmlspecialchars($user_email); ?> <br>
            📘 Total Books: <span id="bookCount">0</span>
          </div>
        </div>
        <div class="search-box mt-3 mt-md-0">
          <input type="text" id="searchBook" class="form-control" placeholder="🔍 Search registered books...">
        </div>
      </div>

      <div class="row">
        <div class="col-md-4" id="myBooksList">
          <h5 class="section-title">📕 My Books</h5>
          <ul id="myBooks" class="list-group"></ul>
        </div>

        <div class="col-md-8">
          <h5 class="section-title">📚 All Available Books</h5>
          <div id="bookList" class="row mt-3"></div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Sidebar toggle
    document.getElementById("menuBtn").addEventListener("click", function() {
      document.getElementById("sidebar").classList.toggle("active");
      document.querySelector(".content").classList.toggle("active");
    });

    $(document).ready(function() {
      loadBooks();

      // Live search with backend fetch
      $("#searchBook").on("keyup", function() {
        const searchQuery = $(this).val().trim();
        loadBooks(searchQuery);
      });
    });

    // Load books from backend (with optional search query)
    function loadBooks(search = "") {
      $("#bookList").html("<p>Loading books...</p>");
      $.ajax({
        url: "../backend/fetch_books.php",
        type: "GET",
        data: { search: search },
        dataType: "json",
        success: function(res) {
          if (res.status === "success") {
            $("#bookCount").text(res.books.filter(b => b.user_id == <?php echo $user_id; ?>).length);

            if (res.books.length === 0) {
              $("#bookList").html("<p>No books found.</p>");
              $("#myBooks").html("<li class='list-group-item'>You haven't added any books yet.</li>");
              return;
            }

            let html = "";
            let myBooksHTML = "";
            res.books.forEach(book => {
              const isMyBook = book.user_id == <?php echo $user_id; ?>;
              if (isMyBook) {
                myBooksHTML += `<li class="list-group-item">📖 <strong>${book.title}</strong> by ${book.author}</li>`;
              }

              html += `
                <div class="col-md-6">
                  <div class="book-card">
                    <img src="../uploads/${book.image || 'default.jpg'}" alt="Book Cover">
                    <h5 class="mt-3">${book.title}</h5>
                    <p><strong>Author:</strong> ${book.author}</p>
                    <p><strong>Genre:</strong> ${book.genre || 'N/A'}</p>
                    <p class="text-muted">${book.description || 'No description available.'}</p>
                    <div class="book-actions">
                      ${isMyBook 
                        ? `<button class="btn btn-danger btn-sm" onclick="deleteBook(${book.id})">🗑 Delete</button>` 
                        : `<button class="btn btn-success btn-sm" onclick="requestTrade(${book.id}, ${book.user_id})">🤝 Trade</button>`}
                    </div>
                  </div>
                </div>`;
            });

            $("#bookList").html(html);
            $("#myBooks").html(myBooksHTML || "<li class='list-group-item'>No books added yet.</li>");
          } else {
            $("#bookList").html(`<p class='text-danger'>⚠️ ${res.message}</p>`);
          }
        },
        error: function() {
          $("#bookList").html("<p class='text-danger'>⚠️ Server error while loading books.</p>");
        }
      });
    }

    function requestTrade(bookId, ownerId) {
      $.ajax({
        url: "../backend/trade.php",
        type: "POST",
        data: { book_id: bookId, owner_id: ownerId },
        dataType: "json",
        success: function(res) { alert(res.message); },
        error: function() { alert("⚠️ Server error while sending trade request."); }
      });
    }

    function deleteBook(bookId) {
      if (!confirm("Are you sure you want to delete this book?")) return;
      $.ajax({
        url: "../backend/delete_book.php",
        type: "POST",
        data: { book_id: bookId },
        dataType: "json",
        success: function(res) {
          alert(res.message);
          if (res.status === "success") loadBooks();
        },
        error: function() {
          alert("⚠️ Server error while deleting book.");
        }
      });
    }
  </script>
</body>
</html>
