<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'] ?? "N/A"; // Store this during login in your session
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
      background: linear-gradient(120deg, #f8faff, #fce4ec);
      background-size: cover;
      font-family: 'Segoe UI', sans-serif;
      color: #2c3e50;
      min-height: 100vh;
      padding: 40px 0;
    }

    .container {
      background: rgba(255, 255, 255, 0.9);
      border-radius: 18px;
      padding: 35px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.08);
      max-width: 1100px;
    }

    h2 {
      font-weight: 700;
      color: #34495e;
    }

    .header-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      margin-bottom: 30px;
    }

    .user-info {
      background: rgba(240, 248, 255, 0.8);
      border-radius: 12px;
      padding: 12px 18px;
      font-size: 0.95rem;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

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

    .btn-warning {
      background: linear-gradient(135deg, #ffe082, #ffd54f);
      border: none;
      color: #444;
      border-radius: 10px;
    }

    .btn-danger {
      background: #ffb3b3;
      border: none;
      color: #fff;
      border-radius: 10px;
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
      height: 220px;
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
  <div class="container">
    <div class="header-bar">
      <div>
        <h2>👋 Welcome, <?php echo htmlspecialchars($user_name); ?></h2>
        <div class="user-info mt-2">
          📧 <?php echo htmlspecialchars($user_email); ?> <br>
          📘 Total Books: <span id="bookCount">0</span>
        </div>
      </div>

      <div class="mt-3 mt-md-0">
        <a href="add_book.html" class="btn btn-primary me-2">➕ Add Book</a>
        <a href="trade_history.php" class="btn btn-warning me-2">🔄 Trade History</a>
        <a href="../backend/logout.php" class="btn btn-danger">🚪 Logout</a>
      </div>
    </div>

    <div id="myBooksList" class="mb-4">
      <h5 class="section-title">📘 My Books</h5>
      <ul id="myBooks" class="list-group"></ul>
    </div>

    <h4 class="section-title">📚 All Available Books</h4>
    <div id="bookList" class="row mt-3"></div>
  </div>

  <script>
    $(document).ready(function() {
      loadBooks();
    });

    function loadBooks() {
      $("#bookList").html("<p>Loading books...</p>");
      $.ajax({
        url: "../backend/fetch_books.php",
        type: "GET",
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
                <div class="col-md-4">
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
        success: function(res) {
          alert(res.message);
        },
        error: function() {
          alert("⚠️ Server error while sending trade request.");
        }
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
