<?php
include("../config/connectdb.php");
// ดึงรายการประมูลทั้งหมด
$result = $conn->query("
    SELECT a.auction_id, a.fish_name, a.start_price, a.current_price, 
           u.user_name AS seller_name, u.image AS fish_image, u.user_id AS seller_id,
           b.bid_amount, b.created_at, bu.user_name AS buyer_name
    FROM auctions a
    JOIN users u ON a.seller_id = u.user_id
    LEFT JOIN bids b ON a.auction_id = b.auction_id
    LEFT JOIN users bu ON b.buyer_id = bu.user_id
    WHERE b.bid_amount = (SELECT MAX(b2.bid_amount) FROM bids b2 WHERE b2.auction_id = a.auction_id)
       OR b.bid_amount IS NULL
");
?>

<!doctype html>
<html lang="en">

<head>
  <title>Fishbid</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 5.3.2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">🐟Fishbid</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
        aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="#">Home</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Page 1
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="#">Page 1-1</a></li>
              <li><a class="dropdown-item" href="#">Page 1-2</a></li>
              <li><a class="dropdown-item" href="#">Page 1-3</a></li>
            </ul>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Page 2</a>
          </li>
        </ul>

        <ul class="navbar-nav">
          <!-- Profile -->
          <li class="nav-item">
            <a class="nav-link" href="../profile.php">
              <i class="bi bi-person-circle"></i> Profile
            </a>
          </li>
          <!-- Logout -->
          <li class="nav-item">
            <a class="nav-link" href="logout.php">
              <i class="bi bi-box-arrow-right"></i> Logout
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Carousel -->
  <div class="container py-5">
    <h3 class="text-center mb-4">Carousel 3 Images Center</h3>

    <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">

        <!-- Slide 1 -->
        <div class="carousel-item active">
          <div class="row justify-content-center">
            <div class="col-md-4 "><img src="https://mpics.mgronline.com/pics/Images/554000000070703.JPEG"
                class="d-block rounded" style="width:400px; height:250px;"></div>
            <div class="col-md-4"><img src="https://mpics.mgronline.com/pics/Images/556000000169401.JPEG"
                class="d-block rounded" style="width:400px; height:250px;"></div>
            <div class="col-md-4"><img
                src="https://static.thairath.co.th/media/4DQpjUtzLUwmJZZSGowLjZfrw1dMgwuSU0XQmWw4YwsC.jpg"
                class="d-block rounded" style="width:400px; height:250px;"></div>
          </div>
        </div>

        <!-- Slide 2 -->
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

      <!-- ปุ่มเลื่อน -->
      <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>

  </div>
  <div class="container">
    <div class="row">
      <div class="col-md-3">
        <div class="card" style="width: 18rem;">
          <img class="card-img-top" src="..." alt="Card image cap">
          <div class="card-body">
            <h5 class="card-title">Card title</h5>
            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's
              content.</p>
            <a href="#" class="btn btn-primary">Go somewhere</a>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="card" style="width: 18rem;">
          <img class="card-img-top" src="..." alt="Card image cap">
          <div class="card-body">
            <h5 class="card-title">Card title</h5>
            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's
              content.</p>
            <a href="#" class="btn btn-primary">Go somewhere</a>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="card" style="width: 18rem;">
          <img class="card-img-top" src="..." alt="Card image cap">
          <div class="card-body">
            <h5 class="card-title">Card title</h5>
            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's
              content.</p>
            <a href="#" class="btn btn-primary">Go somewhere</a>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="card" style="width: 18rem;">
          <img class="card-img-top" src="..." alt="Card image cap">
          <div class="card-body">
            <h5 class="card-title">Card title</h5>
            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's
              content.</p>
            <a href="#" class="btn btn-primary">Go somewhere</a>
          </div>
        </div>
      </div>
      <div class="container mt-5">
        <div class="row">
          <div class="col-md-3">
            <div class="card" style="width: 18rem;">
              <img class="card-img-top" src="..." alt="Card image cap">
              <div class="card-body">
                <h5 class="card-title">Card title</h5>
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the
                  card's
                  content.</p>
                <a href="#" class="btn btn-primary">Go somewhere</a>
              </div>
            </div>
          </div>


          <div class="col-md-3">
            <div class="card" style="width: 18rem;">
              <img class="card-img-top" src="..." alt="Card image cap">
              <div class="card-body">
                <h5 class="card-title">Card title</h5>
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the
                  card's
                  content.</p>
                <a href="#" class="btn btn-primary">Go somewhere</a>
              </div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="card" style="width: 18rem;">
              <img class="card-img-top" src="..." alt="Card image cap">
              <div class="card-body">
                <h5 class="card-title">Card title</h5>
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the
                  card's
                  content.</p>
                <a href="#" class="btn btn-primary">Go somewhere</a>
              </div>
            </div>
          </div>

          <div class="container mt-5">
            <div class="row">
              <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-3 mb-4">
                  <div class="card h-100 shadow-sm">
                    <img src="<?= $row['fish_image'] ?>" class="card-img-top" alt="<?= $row['fish_name'] ?>">
                    <div class="card-body d-flex flex-column">
                      <h5 class="card-title"><?= $row['fish_name'] ?></h5>
                      <p class="card-text">
                        ราคาเริ่มต้น: ฿<?= $row['start_price'] ?><br>
                        ราคาล่าสุด: <strong>฿<?= $row['current_price'] ?? $row['start_price'] ?></strong><br>
                        ผู้ขาย: <?= $row['seller_name'] ?><br>
                        <?php if ($row['buyer_name']): ?>
                          ผู้เสนอราคาล่าสุด: <?= $row['buyer_name'] ?>
                        <?php else: ?>
                          ยังไม่มีผู้เสนอราคา
                        <?php endif; ?>
                      </p>

                      <!-- ปุ่มเสนอราคา -->
                      <form method="POST" action="deal_bid.php" class="mt-auto">
                        <input type="hidden" name="auction_id" value="<?= $row['auction_id'] ?>">
                        <div class="input-group mb-2">
                          <input type="number" step="0.01" name="bid_amount" class="form-control"
                            placeholder="ใส่ราคาเสนอ" required>
                          <button class="btn btn-success" type="submit">เสนอราคา</button>
                        </div>
                      </form>

                      <!-- ปุ่มดูโปรไฟล์ผู้ขาย -->
                      <a href="/niranam/profile.php?user_id=<?= $row['seller_id'] ?>"
                        class="btn btn-primary btn-sm w-100">
                        ดูโปรไฟล์ผู้ขาย
                      </a>

                    </div>
                  </div>
                </div>
              <?php endwhile; ?>
            </div>
          </div>



        </div>
      </div>



    </div>

  </div>



  <!-- Bootstrap JS + icons -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</body>

</html>