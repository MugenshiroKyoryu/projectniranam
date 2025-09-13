<?php
session_start();
include("../config/connectdb.php");

$user_id = $_SESSION['id'] ?? 0;

// Pagination
$itemsPerPage = 8;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $itemsPerPage;

// อ่านแจ้งเตือนยังไม่อ่าน
$notifResult = $conn->query("SELECT COUNT(*) AS unread_count FROM notifications WHERE user_id=$user_id AND is_read=0");
$notifRow = $notifResult->fetch_assoc();
$unreadCount = $notifRow['unread_count'] ?? 0;

// ถ้ามีอ่านแจ้งเตือน (ผ่าน GET ?mark_read=1)
if (isset($_GET['mark_read']) && $_GET['mark_read'] == 1) {
  $conn->query("UPDATE notifications SET is_read=1 WHERE user_id=$user_id");
  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}

// ค้นหา
$searchQuery = $_GET['q'] ?? '';
$searchSQL = $searchQuery ? " AND fish_name LIKE '%" . $conn->real_escape_string($searchQuery) . "%'" : "";

// จำนวนรวมรายการ
$totalResult = $conn->query("SELECT COUNT(*) AS total FROM auctions WHERE 1=1 $searchSQL");
$totalRow = $totalResult->fetch_assoc();
$totalItems = $totalRow['total'];
$totalPages = ceil($totalItems / $itemsPerPage);

// ดึงรายการประมูล
$result = $conn->query("
    SELECT a.auction_id, a.fish_name, a.start_price, a.current_price, 
           u.user_name AS seller_name, u.user_id AS seller_id,
           b.bid_amount, b.created_at, bu.user_name AS buyer_name
    FROM auctions a
    JOIN users u ON a.seller_id = u.user_id
    LEFT JOIN bids b ON b.bid_amount = (
        SELECT MAX(b2.bid_amount)
        FROM bids b2
        WHERE b2.auction_id = a.auction_id
    )
    LEFT JOIN users bu ON b.buyer_id = bu.user_id
    WHERE 1=1 $searchSQL
    ORDER BY COALESCE(a.last_bid_time, a.created_at) DESC
    LIMIT $itemsPerPage OFFSET $offset
");

// AJAX mark read
if (isset($_GET['mark_read_ajax']) && $_GET['mark_read_ajax'] == 1) {
  $conn->query("UPDATE notifications SET is_read=1 WHERE user_id=$user_id");
  echo json_encode(['success' => true]);
  exit;
}

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Fishbid</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">🐟Fishbid</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          
        </ul>
        <form class="d-flex me-3" method="GET" action="">
          <input class="form-control me-2" type="search" name="q" placeholder="ค้นหาปลา..."
            value="<?= htmlspecialchars($searchQuery) ?>">
          <button class="btn btn-outline-light" type="submit">ค้นหา</button>
          <?php if ($searchQuery): ?><a href="home.php" class="btn btn-secondary ms-2">ล้าง</a><?php endif; ?>
        </form>
        <ul class="navbar-nav">
          <li class="nav-item dropdown">
            <a class="nav-link position-relative" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown">
              <i class="bi bi-bell"></i>
              <?php if ($unreadCount > 0): ?>
                <span
                  class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= $unreadCount ?></span>
              <?php endif; ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" style="max-height:300px; overflow-y:auto;">
              <?php
              $notifList = $conn->query("SELECT message, created_at FROM notifications WHERE user_id=$user_id ORDER BY created_at DESC LIMIT 5");
              while ($n = $notifList->fetch_assoc()):
                ?>
                <li>
                  <a class="dropdown-item" href="#"><?= htmlspecialchars($n['message']) ?><br>
                    <small class="text-muted"><?= $n['created_at'] ?></small></a>
                </li>
              <?php endwhile; ?>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li><a class="dropdown-item text-center" href="?mark_read=1">อ่านทั้งหมด</a></li>
            </ul>
          </li>
          <li class="nav-item"><a class="nav-link" href="../add_fish&auction.php"><ii class="bi bi-arrow-bar-up"></i>
              add</a>
          </li>
          <li class="nav-item"><a class="nav-link" href="../profile.php"><i class="bi bi-person-circle"></i> Profile</a>
          </li>
          
          <li class="nav-item"><a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container py-5">
    <h3 class="text-center mb-4">Hello Greater Buyer</h3>

    <!-- Carousel -->
    <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">
        <div class="carousel-item active">
          <div class="row justify-content-center">
            <div class="col-md-4"><img src="https://mpics.mgronline.com/pics/Images/554000000070703.JPEG"
                class="d-block rounded" style="width:400px; height:250px;"></div>
            <div class="col-md-4"><img src="https://mpics.mgronline.com/pics/Images/556000000169401.JPEG"
                class="d-block rounded" style="width:400px; height:250px;"></div>
            <div class="col-md-4"><img
                src="https://static.thairath.co.th/media/4DQpjUtzLUwmJZZSGowLjZfrw1dMgwuSU0XQmWw4YwsC.jpg"
                class="d-block rounded" style="width:400px; height:250px;"></div>
          </div>
        </div>
        <div class="carousel-item">
          <div class="row justify-content-center">
            <div class="col-md-4"><img
                src="https://thematter.co/wp-content/uploads/2019/01/49348331_2202955606586496_6909373455976628224_o.jpg"
                class="d-block rounded" style="width:400px; height:250px;"></div>
            <div class="col-md-4"><img
                src="https://cdni-hw.ch7.com/dm/sz-md/i/images/2023/01/05/63b69a0bd9e3b4.19813260.jpg"
                class="d-block rounded" style="width:400px; height:250px;"></div>
            <div class="col-md-4"><img
                src="https://res.klook.com/image/upload/w_750,h_469,c_fill,q_85/w_80,x_15,y_15,g_south_west,l_Klook_water_br_trans_yhcmh3/activities/ov5ld13juo1i7afd71gq.jpg"
                class="d-block rounded" style="width:400px; height:250px;"></div>
          </div>
        </div>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev"><span
          class="carousel-control-prev-icon"></span></button>
      <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next"><span
          class="carousel-control-next-icon"></span></button>
    </div>
  </div>

  <div class="container mt-5">
    <div class="row">
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="col-md-3 mb-4">
          <div class="card h-100 shadow-sm">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?= htmlspecialchars($row['fish_name']) ?></h5>
              <p class="card-text">
                ราคาเริ่มต้น: ฿<?= $row['start_price'] ?><br>
                ราคาล่าสุด: <strong>฿<?= $row['current_price'] ?? $row['start_price'] ?></strong><br>
                ผู้ขาย: <?= htmlspecialchars($row['seller_name']) ?><br>
                <?php if ($row['buyer_name']): ?>ผู้เสนอราคาล่าสุด:
                  <?= htmlspecialchars($row['buyer_name']) ?>   <?php else: ?>ยังไม่มีผู้เสนอราคา<?php endif; ?>
              </p>

              <form method="POST" action="deal_bid.php" class="mt-auto mb-2">
                <input type="hidden" name="auction_id" value="<?= $row['auction_id'] ?>">
                <div class="input-group mb-2">
                  <input type="number" step="0.01" name="bid_amount" class="form-control" placeholder="ใส่ราคาเสนอ"
                    required>
                  <button class="btn btn-success" type="submit">เสนอราคา</button>
                </div>
              </form>

              <a href="../profile.php?user_id=<?= $row['seller_id'] ?>"
                class="btn btn-primary btn-sm mb-2 w-100">ดูโปรไฟล์ผู้ขาย</a>
              <a href="../fish_detail.php?auction_id=<?= $row['auction_id'] ?>"
                class="btn btn-info btn-sm w-100">ดูรายละเอียดปลา</a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>

  <nav aria-label="Page navigation">
    <ul class="pagination justify-content-center mt-4">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
          <a class="page-link"
            href="?page=<?= $i ?><?= $searchQuery ? '&q=' . urlencode($searchQuery) : '' ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const notifDropdown = document.getElementById('notifDropdown');
      notifDropdown.addEventListener('click', function () {
        fetch('<?= $_SERVER['PHP_SELF'] ?>?mark_read_ajax=1')
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              const badge = notifDropdown.querySelector('span.badge');
              if (badge) badge.remove(); // ลบ badge สีแดง
            }
          });
      });
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php $conn->close(); ?>