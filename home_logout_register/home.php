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
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
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
              <div class="col-md-4 "><img src="https://mpics.mgronline.com/pics/Images/554000000070703.JPEG" class="d-block rounded" style="width:400px; height:250px;"></div>
              <div class="col-md-4"><img src="https://mpics.mgronline.com/pics/Images/556000000169401.JPEG" class="d-block rounded" style="width:400px; height:250px;"></div>
              <div class="col-md-4"><img src="https://static.thairath.co.th/media/4DQpjUtzLUwmJZZSGowLjZfrw1dMgwuSU0XQmWw4YwsC.jpg" class="d-block rounded" style="width:400px; height:250px;"></div>
            </div>
          </div>

          <!-- Slide 2 -->
          <div class="carousel-item">
            <div class="row justify-content-center">
              <div class="col-md-4"><img src="https://thematter.co/wp-content/uploads/2019/01/49348331_2202955606586496_6909373455976628224_o.jpg" class="d-block rounded" style="width:400px; height:250px;"></div>
              <div class="col-md-4"><img src="https://cdni-hw.ch7.com/dm/sz-md/i/images/2023/01/05/63b69a0bd9e3b4.19813260.jpg" class="d-block rounded" style="width:400px; height:250px;"></div>
              <div class="col-md-4"><img src="https://res.klook.com/image/upload/w_750,h_469,c_fill,q_85/w_80,x_15,y_15,g_south_west,l_Klook_water_br_trans_yhcmh3/activities/ov5ld13juo1i7afd71gq.jpg" class="d-block rounded" style="width:400px; height:250px;"></div>
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

    <!-- Bootstrap JS + icons -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  </body>
</html>
