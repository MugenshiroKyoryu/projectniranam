<?php
session_start();
include("config/connectdb.php");

// ตรวจสอบ login
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit;
}

// ตรวจสอบ auction_id
if (!isset($_GET['auction_id']) || empty($_GET['auction_id'])) {
    echo "No auction selected.";
    exit;
}
$auction_id = intval($_GET['auction_id']);

// ดึงข้อมูล auction + fish + ผู้ขาย + bids ล่าสุด
$stmt = $conn->prepare("
    SELECT 
        f.*, 
        u.user_name AS seller_name,
        u.user_id AS seller_id,
        u.image AS seller_image,
        u.user_status AS seller_status,
        u.facebook AS seller_facebook,
        u.twitter AS seller_twitter,
        u.instagram AS seller_instagram,
        u.github AS seller_github,
        u.description AS seller_description,
        a.auction_id,
        a.start_price,
        a.current_price,
        b.bid_amount,
        bu.user_name AS buyer_name,
        b.created_at AS bid_time
    FROM auctions a
    JOIN fish f ON a.fish_name = f.fish_name
    LEFT JOIN users u ON a.seller_id = u.user_id
    LEFT JOIN bids b ON b.bid_amount = (
        SELECT MAX(b2.bid_amount) FROM bids b2 WHERE b2.auction_id = a.auction_id
    )
    LEFT JOIN users bu ON b.buyer_id = bu.user_id
    WHERE a.auction_id = ?
");
$stmt->bind_param("i", $auction_id);
$stmt->execute();
$fish = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$fish) {
    echo "Auction/Fish not found.";
    exit;
}

// ฟังก์ชันดึง username ของ social จาก URL
function getSocialUsername($url) {
    $parts = parse_url($url);
    if (!isset($parts['host'])) return $url;

    $hosts = [
        'facebook.com' => 'facebook.com/',
        'www.facebook.com' => 'facebook.com/',
        'twitter.com' => 'twitter.com/',
        'www.twitter.com' => 'twitter.com/',
        'instagram.com' => 'instagram.com/',
        'www.instagram.com' => 'instagram.com/',
        'github.com' => 'github.com/',
        'www.github.com' => 'github.com/',
    ];

    $host = $parts['host'];
    $path = trim($parts['path'], '/');
    return isset($hosts[$host]) ? ($path ?: $host) : $url;
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($fish['fish_name']) ?> - Fish Detail</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<style>
body { background-color: #eeeeee; }
.fish-img { width: 100%; height: 300px; object-fit: cover; border-radius: 10px; }
.profile-avatar { 
    width: 150px; 
    height: 150px; 
    object-fit: cover; 
    border-radius: 50%; 
    border: 2px solid #ddd; 
    display: block; 
    margin: 0 auto; 
}
.list-group-item a { color: inherit; text-decoration: none; font-size: 1.1rem; font-weight: 500; }
.list-group-item a:hover { text-decoration: underline; color: inherit; }
</style>
</head>
<body>
<main>
<div class="container py-5">

    <a href="home_logout_register/home.php" class="btn btn-secondary mb-3"><i class="bi bi-arrow-left"></i> กลับหน้าหลัก</a>

    <div class="row">
        <!-- Fish Info -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm">
                <img src="<?= htmlspecialchars($fish['fish_image'] ?? 'https://via.placeholder.com/600x300') ?>" class="card-img-top fish-img" alt="Fish Image">
                <div class="card-body">
                    <h3 class="card-title"><?= htmlspecialchars($fish['fish_name']) ?></h3>
                    <p><strong>ประเภทปลา:</strong> <?= htmlspecialchars($fish['fish_type'] ?? '-') ?></p>
                    <p><strong>ขนาด:</strong> <?= htmlspecialchars($fish['fish_size'] ?? '-') ?></p>
                    <p><strong>อายุ:</strong> <?= htmlspecialchars($fish['fish_age'] ?? '-') ?> ปี</p>
                    <p><strong>สุขภาพ:</strong> <?= htmlspecialchars($fish['fish_health'] ?? '-') ?></p>
                    <p><strong>ที่อยู่แหล่งน้ำ:</strong> <?= htmlspecialchars($fish['habitat'] ?? '-') ?></p>
                    <p><strong>รายละเอียดเพิ่มเติม:</strong><br><?= nl2br(htmlspecialchars($fish['fish_description'])) ?></p>
                    <?php if (!empty($fish['other_attributes'])): ?>
                        <p><strong>คุณสมบัติอื่นๆ:</strong><br><?= nl2br(htmlspecialchars($fish['other_attributes'])) ?></p>
                    <?php endif; ?>

                    <?php if (!empty($fish['auction_id'])): ?>
                    <hr>
                    <h5>ข้อมูลประมูล</h5>
                    <p>
                        ราคาเริ่มต้น: ฿<?= $fish['start_price'] ?><br>
                        ราคาปัจจุบัน: <strong>฿<?= $fish['current_price'] ?? $fish['start_price'] ?></strong><br>
                        <?php if (!empty($fish['buyer_name'])): ?>
                            ผู้เสนอราคาล่าสุด: <?= htmlspecialchars($fish['buyer_name']) ?><br>
                            เวลา: <?= $fish['bid_time'] ?>
                        <?php else: ?>
                            ยังไม่มีผู้เสนอราคา
                        <?php endif; ?>
                    </p>
                    <form method="POST" action="home_logout_register/deal_bid.php" class="mt-3">
                        <input type="hidden" name="auction_id" value="<?= $fish['auction_id'] ?>">
                        <div class="input-group mb-2">
                            <input type="number" step="0.01" name="bid_amount" class="form-control" placeholder="ใส่ราคาเสนอ" required>
                            <button class="btn btn-success" type="submit">เสนอราคา</button>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Seller Profile -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4 text-center">
                <?php
                $avatarSrc = !empty($fish['seller_image']) && file_exists('uploads/' . $fish['seller_image'])
                    ? 'uploads/' . $fish['seller_image']
                    : 'https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava3.webp';
                ?>
                <img src="<?= htmlspecialchars($avatarSrc) ?>" class="profile-avatar mt-3" alt="Seller Avatar">
                <div class="card-body">
                    <h5><?= htmlspecialchars($fish['seller_name'] ?? 'ไม่ระบุ') ?></h5>
                    <p class="text-muted"><?= htmlspecialchars($fish['seller_status'] ?? '') ?></p>

                    <!-- Social links แบบเดียวกับหน้า user profile -->
                    <div class="list-group list-group-flush rounded-3 mt-3">
                        <?php
                        $socials = [
                            'facebook' => ['url' => $fish['seller_facebook'] ?? '', 'icon' => 'bi-facebook text-primary'],
                            'twitter' => ['url' => $fish['seller_twitter'] ?? '', 'icon' => 'bi-twitter text-info'],
                            'instagram' => ['url' => $fish['seller_instagram'] ?? '', 'icon' => 'bi-instagram text-danger'],
                            'github' => ['url' => $fish['seller_github'] ?? '', 'icon' => 'bi-github text-dark']
                        ];
                        foreach ($socials as $key => $data):
                            if (!empty($data['url'])):
                                $username = getSocialUsername($data['url']);
                        ?>
                            <a href="<?= htmlspecialchars($data['url']) ?>" target="_blank" class="list-group-item d-flex align-items-center">
                                <i class="bi <?= $data['icon'] ?> fs-4 me-2"></i> <?= htmlspecialchars($username) ?>
                            </a>
                        <?php endif; endforeach; ?>
                    </div>

                    <?php if(!empty($fish['seller_description'])): ?>
                        <p class="mt-3"><?= nl2br(htmlspecialchars($fish['seller_description'])) ?></p>
                    <?php endif; ?>

                    <a href="profile.php?user_id=<?= $fish['seller_id'] ?>" class="btn btn-primary w-100 mt-2">ดูโปรไฟล์ผู้ขาย</a>
                </div>
            </div>
        </div>
    </div>
</div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
