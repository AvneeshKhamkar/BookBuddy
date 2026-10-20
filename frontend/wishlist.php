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
  <title>Wishlist | BookBuddy</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

  <style>
    body {
      margin: 0;
      min-height: 100vh;
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #fce2db 0%, #e7f0ff 40%, #e5f8f6 70%, #fde6f2 100%);
      background-attachment: fixed;
    }
    .container-box {
      max-width: 1100px;
      margin: 40px auto;
      background: rgba(255,255,255,0.78);
      backdrop-filter: blur(12px);
      border-radius: 22px;
      padding: 25px;
      box-shadow: 0 16px 44px rgba(0,0,0,0.08);
    }
    .book-card {
      background: rgba(255,255,255,0.92);
      border-radius: 18px;
      overflow: hidden;
      box-shadow: 0 12px 30px rgba(0,0,0,0.08);
      transition: 0.25s;
      height: 100%;
    }
    .book-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 22px 55px rgba(0,0,0,0.12);
    }
    .book-cover {
      height: 220px;
      width: 100%;
      object-fit: cover;
    }
    .pill {
      font-size: 12px;
      font-weight: 800;
      padding: 6px 10px;
      border-radius: 999px;
    }
    .pill-new { background: #e7f7ed; color: #1a7f37; }
    .pill-good { background: #eef6ff; color: #0969da; }
    .pill-old { background: #fff4e5; color: #9a6700; }
    .rating { font-weight: 900; color: #f39c12; }
    .btn-soft { border-radius: 14px; font-weight: 700; padding: 8px 12px; }
  </style>
</head>

<body>
  <div class="container-box">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
      <div>
        <h2 class="mb-0">❤️ Wishlist</h2>
        <small class="text-muted">Saved books for <?php echo htmlspecialchars($user_name); ?></small>
      </div>
      <a href="dashboard.php" class="btn btn-secondary btn-soft">⬅ Back to Dashboard</a>
    </div>

    <div class="row g-4" id="wishlistBooks">
      <p>Loading wishlist...</p>
    </div>
  </div>

<script>
  function badge(condition) {
    const c = (condition || "Good").toLowerCase();
    if (c === "new") return `<span class="pill pill-new">NEW</span>`;
    if (c === "old") return `<span class="pill pill-old">OLD</span>`;
    return `<span class="pill pill-good">GOOD</span>`;
  }

  function loadWishlist() {
    $("#wishlistBooks").html("<p>Loading wishlist...</p>");

    $.ajax({
      url: "../backend/wishlist.php",
      type: "GET",
      data: { action: "list" },
      dataType: "json",
      success: function(res) {
        if (res.status !== "success") {
          $("#wishlistBooks").html(`<p class="text-danger">${res.message}</p>`);
          return;
        }

        const books = res.wishlist;

        if (!books || books.length === 0) {
          $("#wishlistBooks").html("<p class='text-muted'>No wishlist books yet ❤️</p>");
          return;
        }

        let html = "";
        books.forEach(book => {
          const img = book.image ? `../uploads/${book.image}` : "https://via.placeholder.com/300x220?text=No+Image";

          html += `
            <div class="col-md-4">
              <div class="book-card">
                <img src="${img}" class="book-cover">
                <div class="p-3">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    ${badge(book.book_condition)}
                    <span class="rating">⭐ ${book.rating || 5}/5</span>
                  </div>
                  <h5 class="fw-bold mb-1">${book.title}</h5>
                  <div class="text-muted"><b>Author:</b> ${book.author}</div>
                  <div class="text-muted"><b>Genre:</b> ${book.genre || "N/A"}</div>
                  <div class="small text-secondary mt-2">${book.description || ""}</div>

                  <div class="d-flex gap-2 mt-3 flex-wrap">
                    <button class="btn btn-danger btn-sm btn-soft" onclick="removeWishlist(${book.book_id})">❌ Remove</button>
                  </div>
                </div>
              </div>
            </div>
          `;
        });

        $("#wishlistBooks").html(html);
      },
      error: function() {
        $("#wishlistBooks").html("<p class='text-danger'>⚠️ Server error loading wishlist.</p>");
      }
    });
  }

  function removeWishlist(bookId) {
    $.ajax({
      url: "../backend/wishlist.php",
      type: "POST",
      data: { action: "toggle", book_id: bookId },
      dataType: "json",
      success: function(res) {
        alert(res.message);
        loadWishlist();
      },
      error: function() {
        alert("⚠️ Server error while removing wishlist.");
      }
    });
  }

  $(document).ready(function() {
    loadWishlist();
  });
</script>
</body>
</html>
