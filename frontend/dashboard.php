<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard | BookBuddy</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <style>
    body { background-color: #f8f9fa; }
    .book-card {
      border: 1px solid #ddd;
      border-radius: 10px;
      padding: 15px;
      background: white;
      margin-bottom: 20px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .book-card img {
      width: 100%;
      height: 220px;
      object-fit: cover;
      border-radius: 10px;
    }
    .book-actions { margin-top: 10px; }
    .btn-trade { background-color: #198754; color: white; }
    .btn-delete { background-color: #dc3545; color: white; }
    #myBooksList {
      background: #fff;
      padding: 10px;
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
      <h2>📚 Welcome, <?php echo htmlspecialchars($user_name); ?>!</h2>
      <div>
        <a href="add_book.html" class="btn btn-primary">➕ Add Book</a>
        <a href="trade_history.php" class="btn btn-warning">🔄 Trade History</a>
        <a href="../backend/logout.php" class="btn btn-danger">🚪 Logout</a>
      </div>
    </div>

    <!-- ✅ My Books Section -->
    <div id="myBooksList" class="mb-4">
      <h5>📘 My Books (for Trade Reference)</h5>
      <ul id="myBooks" class="list-group"></ul>
    </div>

    <h4>All Available Books</h4>
    <div id="bookList" class="row mt-3"></div>
  </div>

  <script>
    $(document).ready(function() {
      loadBooks();
    });

    // ✅ Load books via AJAX
    function loadBooks() {
      $("#bookList").html("<p>Loading books...</p>");
      $.ajax({
        url: "../backend/fetch_books.php",
        type: "GET",
        dataType: "json",
        success: function(res) {
          console.log("✅ Fetched books:", res);

          if (res.status === "success") {
            if (res.books.length === 0) {
              $("#bookList").html("<p>No books found.</p>");
              $("#myBooks").html("<li class='list-group-item'>You haven't added any books yet.</li>");
              return;
            }

            let html = "";
            let myBooksHTML = "";
            res.books.forEach(book => {
              const isMyBook = book.user_id == <?php echo $user_id; ?>;

              // ✅ Add to “My Books” list
              if (isMyBook) {
                myBooksHTML += `<li class="list-group-item">ID: <strong>${book.id}</strong> — ${book.title} by ${book.author}</li>`;
              }

              // ✅ Display all books
              html += `
                <div class="col-md-4">
                  <div class="book-card">
                    <img src="../uploads/${book.image || 'default.jpg'}" alt="Book Cover">
                    <h5 class="mt-2">${book.title}</h5>
                    <p><strong>Author:</strong> ${book.author}</p>
                    <p><strong>Genre:</strong> ${book.genre || 'N/A'}</p>
                    <p>${book.description || ''}</p>
                    <div class="book-actions">
                      ${isMyBook 
                        ? `<button class="btn btn-sm btn-delete" onclick="deleteBook(${book.id})">🗑 Delete</button>`
                        : `<button class="btn btn-sm btn-trade" onclick="requestTrade(${book.id}, ${book.user_id})">🤝 Trade</button>`}
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
        error: function(xhr, status, error) {
          console.error("❌ AJAX Error:", error);
          $("#bookList").html("<p class='text-danger'>⚠️ Server error while loading books.</p>");
        }
      });
    }

    // ✅ Request Trade
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

    // ✅ Delete Book
    function deleteBook(bookId) {
      if (!confirm("Are you sure you want to delete this book?")) return;

      $.ajax({
        url: "../backend/delete_book.php",
        type: "POST",
        data: { book_id: bookId },
        dataType: "json",
        success: function(res) {
          alert(res.message);
          if (res.status === "success") {
            loadBooks();
          }
        },
        error: function() {
          alert("⚠️ Server error while deleting book.");
        }
      });
    }
  </script>
</body>
</html>
