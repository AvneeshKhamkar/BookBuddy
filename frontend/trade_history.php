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
  <title>Trade History | BookBuddy</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <style>
    body { background-color: #f8f9fa; }
    .trade-card {
      border: 1px solid #ddd;
      border-radius: 10px;
      background: #fff;
      padding: 15px;
      margin-bottom: 15px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    small { color: #555; font-style: italic; }
  </style>
</head>
<body>
  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>🔄 Trade History for <?php echo htmlspecialchars($user_name); ?></h2>
      <a href="dashboard.php" class="btn btn-secondary">⬅️ Back to Dashboard</a>
    </div>

    <div id="tradeList">Loading trades...</div>

    <!-- ✅ Modal for choosing trade type -->
    <div class="modal fade" id="tradeTypeModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">🤝 Complete Trade</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form id="tradeTypeForm">
              <input type="hidden" id="modalTradeId">
              <div class="mb-3">
                <label class="form-label">Choose trade type:</label>
                <select class="form-select" id="tradeTypeSelect">
                  <option value="">-- Select Type --</option>
                  <option value="book">Exchange Book</option>
                  <option value="money">Money Trade</option>
                </select>
              </div>

              <div id="bookTradeOptions" class="mb-3" style="display:none;">
                <label class="form-label">Select one of your books to trade:</label>
                <select class="form-select" id="myBookDropdown"></select>
              </div>

              <div id="moneyTradeOptions" class="mb-3" style="display:none;">
                <label class="form-label">Enter Amount (₹)</label>
                <input type="number" class="form-control" id="offeredPrice" placeholder="e.g. 250">
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-success" id="confirmTradeBtn">✅ Confirm</button>
          </div>
        </div>
      </div>
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    let myBooks = [];

    $(document).ready(function() {
      loadTrades();
      loadMyBooks();
    });

    // ✅ Load trade requests
    function loadTrades() {
      $.ajax({
        url: "../backend/get_trades.php",
        type: "GET",
        dataType: "json",
        success: function(res) {
          if (res.status === "success") {
            if (res.trades.length === 0) {
              $("#tradeList").html("<p>No trade requests yet.</p>");
              return;
            }

            let html = "";
            res.trades.forEach(t => {
              let tradeDetail = "";
              if (t.status === "accepted") {
                if (t.trade_type === "book" && t.offered_book_title) {
                  tradeDetail = `<p><strong>Traded for Book:</strong> ${t.offered_book_title}</p>`;
                } else if (t.trade_type === "money" && t.offered_price) {
                  tradeDetail = `<p><strong>Traded for:</strong> ₹${t.offered_price}</p>`;
                }
              }

              html += `
                <div class="trade-card">
                  <h5>📘 ${t.book_title}</h5>
                  <p><strong>Requester:</strong> ${t.requester_name} <br><small>📧 ${t.requester_email}</small></p>
                  <p><strong>Owner:</strong> ${t.owner_name} <br><small>📧 ${t.owner_email}</small></p>
                  <p><strong>Status:</strong>
                    <span class="badge ${t.status === 'accepted' ? 'bg-success' : t.status === 'declined' ? 'bg-danger' : 'bg-warning'}">
                      ${t.status}
                    </span>
                  </p>
                  ${tradeDetail}
                  <p><small>${t.created_at}</small></p>
                  ${t.owner_name === "<?php echo $user_name; ?>" && t.status === "pending" ? `
                    <button class="btn btn-success btn-sm" onclick="openTradeModal(${t.id})">Accept</button>
                    <button class="btn btn-danger btn-sm" onclick="updateTrade(${t.id}, 'declined')">Decline</button>
                  ` : ""}
                </div>`;
            });
            $("#tradeList").html(html);
          } else {
            $("#tradeList").html(`<p class='text-danger'>${res.message}</p>`);
          }
        },
        error: function() {
          $("#tradeList").html("<p class='text-danger'>⚠️ Server error loading trades.</p>");
        }
      });
    }

    // ✅ Load user's books for dropdown
    function loadMyBooks() {
      $.ajax({
        url: "../backend/fetch_books.php",
        type: "GET",
        dataType: "json",
        success: function(res) {
          if (res.status === "success") {
            myBooks = res.books.filter(b => b.user_id == <?php echo $user_id; ?>);
          }
        }
      });
    }

    // ✅ Open modal for selecting trade type
    function openTradeModal(tradeId) {
      $("#modalTradeId").val(tradeId);
      $("#tradeTypeSelect").val("");
      $("#bookTradeOptions, #moneyTradeOptions").hide();
      $("#offeredPrice").val("");

      // Populate book dropdown
      const dropdown = $("#myBookDropdown");
      dropdown.empty();
      dropdown.append(`<option value="">-- Select Your Book --</option>`);
      myBooks.forEach(b => {
        dropdown.append(`<option value="${b.id}">${b.title} by ${b.author}</option>`);
      });

      const modal = new bootstrap.Modal(document.getElementById("tradeTypeModal"));
      modal.show();
    }

    // ✅ Toggle options
    $("#tradeTypeSelect").on("change", function() {
      const type = $(this).val();
      if (type === "book") {
        $("#bookTradeOptions").show();
        $("#moneyTradeOptions").hide();
      } else if (type === "money") {
        $("#moneyTradeOptions").show();
        $("#bookTradeOptions").hide();
      } else {
        $("#bookTradeOptions, #moneyTradeOptions").hide();
      }
    });

    // ✅ Confirm trade
    $("#confirmTradeBtn").on("click", function() {
      const tradeId = $("#modalTradeId").val();
      const type = $("#tradeTypeSelect").val();
      const bookId = $("#myBookDropdown").val();
      const price = $("#offeredPrice").val();

      if (!type) {
        alert("Please select trade type.");
        return;
      }

      if (type === "book" && !bookId) {
        alert("Please select a book to trade.");
        return;
      }

      if (type === "money" && !price) {
        alert("Please enter an amount.");
        return;
      }

      $.ajax({
        url: "../backend/update_trade.php",
        type: "POST",
        data: {
          trade_id: tradeId,
          status: "accepted",
          trade_type: type,
          offered_book_id: bookId,
          offered_price: price
        },
        dataType: "json",
        success: function(res) {
          alert(res.message);
          $("#tradeTypeModal").modal("hide");
          loadTrades();
        },
        error: function() {
          alert("⚠️ Server error while updating trade.");
        }
      });
    });

    // ✅ Decline trade
    function updateTrade(tradeId, status) {
      $.ajax({
        url: "../backend/update_trade.php",
        type: "POST",
        data: { trade_id: tradeId, status: status },
        dataType: "json",
        success: function(res) {
          alert(res.message);
          loadTrades();
        },
        error: function() {
          alert("⚠️ Server error while declining trade.");
        }
      });
    }
  </script>
</body>
</html>
