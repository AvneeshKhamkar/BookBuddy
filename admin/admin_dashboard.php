<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
  header("Location: admin_login.html");
  exit;
}
$admin_name = $_SESSION['admin_name'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>📊 Admin Dashboard | BookBuddy</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

  <style>
    body {
      background: linear-gradient(120deg, #e0f7fa, #fce4ec);
      background-attachment: fixed;
      min-height: 100vh;
      font-family: 'Segoe UI', sans-serif;
    }

    .navbar {
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(10px);
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .navbar-brand {
      font-weight: bold;
      color: #0077b6 !important;
    }

    .dashboard-container {
      max-width: 1200px;
      margin: 40px auto;
      padding: 20px;
      background: white;
      border-radius: 15px;
      box-shadow: 0 4px 25px rgba(0,0,0,0.1);
    }

    .stat-box {
      background: linear-gradient(135deg, #a8edea, #fed6e3);
      border-radius: 12px;
      padding: 20px;
      text-align: center;
      color: #2c3e50;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
      transition: transform 0.3s;
    }

    .stat-box:hover { transform: translateY(-5px); }

    .book-card, .user-card {
      border: none;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(0,0,0,0.08);
      transition: transform 0.3s;
      background: #fff;
    }

    .book-card:hover, .user-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 6px 18px rgba(0,0,0,0.12);
    }

    .book-card img {
      width: 100%;
      height: 220px;
      object-fit: cover;
      border-bottom: 1px solid #eee;
    }

    footer {
      text-align: center;
      padding: 15px 0;
      color: #6c757d;
      font-size: 0.9rem;
    }

    .nav-tabs .nav-link.active {
      background-color: #0077b6 !important;
      color: white !important;
      border: none;
    }
  </style>
</head>

<body>
  <!-- 🌟 Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
      <a class="navbar-brand" href="#">📚 BookBuddy Admin</a>
      <div class="d-flex align-items-center">
        <span class="me-3 fw-semibold">👋 Welcome, <?php echo htmlspecialchars($admin_name); ?></span>
        <a href="../backend/logout.php" class="btn btn-danger btn-sm">Logout</a>
      </div>
    </div>
  </nav>

  <!-- 📊 Dashboard Content -->
  <div class="dashboard-container">
    <ul class="nav nav-tabs mb-4" id="adminTabs">
      <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#booksTab">📘 Books</button>
      </li>
      <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#usersTab">👥 Users</button>
      </li>
    </ul>

    <div class="tab-content">
      <!-- 📚 BOOKS TAB -->
      <div class="tab-pane fade show active" id="booksTab">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
          <h3 class="fw-bold text-primary">📘 Books Overview</h3>
          <input type="text" id="searchBook" class="form-control w-50" placeholder="🔍 Search by title, author, or owner...">
        </div>

        <div class="row mb-4 text-center">
          <div class="col-md-4 mb-3">
            <div class="stat-box"><h4 id="totalBooks">0</h4><p>Total Books</p></div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="stat-box"><h4 id="uniqueUsers">0</h4><p>Unique Users</p></div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="stat-box"><h4 id="genresCount">0</h4><p>Genres Covered</p></div>
          </div>
        </div>

        <div id="booksContainer" class="row g-4">
          <p>Loading books...</p>
        </div>
      </div>

      <!-- 👥 USERS TAB -->
      <div class="tab-pane fade" id="usersTab">
        <h3 class="fw-bold text-primary mb-3">👥 Registered Users</h3>
        <div id="usersContainer" class="row g-4">
          <p>Loading users...</p>
        </div>
      </div>
    </div>
  </div>

  <footer>© <?php echo date('Y'); ?> BookBuddy Admin Panel. All rights reserved.</footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    let allBooks = [];
    let allUsers = [];

    $(document).ready(function() {
      loadBooks();
      loadUsers();

      $("#searchBook").on("input", function() {
        const query = $(this).val().toLowerCase();
        const filtered = allBooks.filter(book =>
          book.title.toLowerCase().includes(query) ||
          book.author.toLowerCase().includes(query) ||
          book.owner_name.toLowerCase().includes(query)
        );
        renderBooks(filtered);
      });
    });

    // ✅ Load Books
    function loadBooks() {
      $.ajax({
        url: "../backend/fetch_books.php",
        type: "GET",
        dataType: "json",
        success: function(res) {
          if (res.status === "success") {
            allBooks = res.books;
            renderBooks(allBooks);
            updateStats(allBooks);
          } else {
            $("#booksContainer").html(`<p class='text-danger'>⚠️ ${res.message}</p>`);
          }
        },
        error: function() {
          $("#booksContainer").html("<p class='text-danger'>⚠️ Error loading books.</p>");
        }
      });
    }

    function renderBooks(books) {
      const container = $("#booksContainer");
      container.empty();
      if (books.length === 0) return container.html("<p>No books found.</p>");
      books.forEach(b => {
        const image = `../uploads/${b.image || 'default.jpg'}`;
        const card = `
          <div class="col-md-4">
            <div class="book-card">
              <img src="${image}" alt="Book Cover">
              <div class="card-body p-3">
                <h5>${b.title}</h5>
                <p><strong>Author:</strong> ${b.author}</p>
                <p><strong>Genre:</strong> ${b.genre || 'N/A'}</p>
                <p class="text-muted small">${b.description || ''}</p>
                <hr>
                <p><strong>Owner:</strong> ${b.owner_name}</p>
                <p class="text-muted small">${b.owner_email}</p>
              </div>
            </div>
          </div>`;
        container.append(card);
      });
    }

    function updateStats(books) {
      $("#totalBooks").text(books.length);
      $("#uniqueUsers").text(new Set(books.map(b => b.owner_email)).size);
      $("#genresCount").text(new Set(books.map(b => b.genre)).size);
    }

    // ✅ Load Users
    function loadUsers() {
      $.ajax({
        url: "../backend/fetch_users.php",
        type: "GET",
        dataType: "json",
        success: function(res) {
          if (res.status === "success") {
            allUsers = res.users;
            renderUsers(allUsers);
          } else {
            $("#usersContainer").html(`<p class='text-danger'>⚠️ ${res.message}</p>`);
          }
        },
        error: function() {
          $("#usersContainer").html("<p class='text-danger'>⚠️ Error loading users.</p>");
        }
      });
    }

    function renderUsers(users) {
      const container = $("#usersContainer");
      container.empty();
      if (users.length === 0) return container.html("<p>No users found.</p>");
      users.forEach(u => {
        const card = `
          <div class="col-md-4">
            <div class="user-card p-3">
              <h5 class="text-primary">${u.name}</h5>
              <p><strong>Email:</strong> ${u.email}</p>
              <p><strong>Total Books:</strong> ${u.total_books}</p>
            </div>
          </div>`;
        container.append(card);
      });
    }
  </script>
</body>
</html>
