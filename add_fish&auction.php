<?php
// -------------------- เปิด error display --------------------
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include_once 'config/connectdb.php';

// ตรวจสอบล็อกอิน
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit;
}

$seller_id = $_SESSION['id'];

// -------------------- อัปโหลดรูปปลา --------------------
$fish_image = '';
if (isset($_FILES['fish_image'])) {
    $file = $_FILES['fish_image'];
    $uploadDir = 'fish_image/';

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (in_array($file['type'], $allowedTypes) && $file['error'] === 0) {
        $filename = time() . '_' . basename($file['name']);
        $target = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $target)) {
            $fish_image = $filename;
        } else {
            $error = "Upload failed!";
        }
    } else {
        $error = "Invalid file type!";
    }
}

// -------------------- เพิ่มปลาและประมูล --------------------
if (isset($_POST['submit'])) {

    $fish_name = mysqli_real_escape_string($conn, $_POST['fish_name']);
    $fish_type = mysqli_real_escape_string($conn, $_POST['fish_type']);
    $fish_description = mysqli_real_escape_string($conn, $_POST['fish_description']);
    $fish_size = mysqli_real_escape_string($conn, $_POST['fish_size']);
    $fish_age = intval($_POST['fish_age']);
    $fish_health = mysqli_real_escape_string($conn, $_POST['fish_health']);
    $habitat = mysqli_real_escape_string($conn, $_POST['habitat']);
    $breeder = mysqli_real_escape_string($conn, $_POST['breeder']);
    $start_price = floatval($_POST['start_price']); // ดึงค่าที่ผู้ใช้กรอก

    $created_at = date("Y-m-d H:i:s");
    $last_bid_time = $created_at;

    mysqli_begin_transaction($conn);

    try {
        // -------------------- เพิ่มปลา --------------------
        $fish_sql = "INSERT INTO fish 
            (fish_name, fish_type, fish_image, fish_description, fish_size, fish_age, fish_health, habitat, breeder, created_at)
            VALUES 
            ('$fish_name', '$fish_type', '$fish_image', '$fish_description', '$fish_size', $fish_age, '$fish_health', '$habitat', '$breeder', '$created_at')";

        if (!mysqli_query($conn, $fish_sql)) {
            throw new Exception("เพิ่มข้อมูลปลาไม่สำเร็จ: " . mysqli_error($conn));
        }

        $fish_id = mysqli_insert_id($conn);

        // -------------------- เพิ่ม auction --------------------
        $auction_sql = "INSERT INTO auctions
            (seller_id, fish_name, start_price, current_price, created_at, last_bid_time, fish_id)
            VALUES
            ($seller_id, '$fish_name', $start_price, $start_price, '$created_at', '$last_bid_time', $fish_id)";

        if (!mysqli_query($conn, $auction_sql)) {
            throw new Exception("เพิ่มข้อมูลประมูลไม่สำเร็จ: " . mysqli_error($conn));
        }

        mysqli_commit($conn);

        // -------------------- redirect ไปหน้า home --------------------
        header("Location: home_logout_register/home.php");
        exit;

    } catch(Exception $e) {
        mysqli_rollback($conn);
        $error = $e->getMessage();
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Add Fish & Auction</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f5f5f5; }
        .preview-img { width:150px; height:150px; object-fit:cover; border:2px solid #ddd; border-radius:10px; }
    </style>
</head>
<body>
<div class="container py-5">
    <h2 class="mb-4">เพิ่มปลาและประมูล</h2>

    <?php if(!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <h4>ข้อมูลปลา</h4>
        <div class="mb-3">
            <label>ชื่อปลา</label>
            <input type="text" name="fish_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>ชนิดปลา</label>
            <input type="text" name="fish_type" class="form-control">
        </div>
        <div class="mb-3">
            <label>รูปปลา</label>
            <input type="file" name="fish_image" accept="image/*" class="form-control" onchange="previewImage(event)">
            <img id="preview" class="preview-img mt-2" src="#" alt="Preview" style="display:none;">
        </div>
        <div class="mb-3">
            <label>รายละเอียด</label>
            <textarea name="fish_description" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label>ขนาด</label>
            <input type="text" name="fish_size" class="form-control">
        </div>
        <div class="mb-3">
            <label>อายุ</label>
            <input type="number" name="fish_age" class="form-control">
        </div>
        <div class="mb-3">
            <label>สุขภาพ</label>
            <input type="text" name="fish_health" class="form-control">
        </div>
        <div class="mb-3">
            <label>แหล่งที่อยู่อาศัย</label>
            <input type="text" name="habitat" class="form-control">
        </div>
        <div class="mb-3">
            <label>ผู้เพาะพันธุ์</label>
            <input type="text" name="breeder" class="form-control">
        </div>

        <h4>ข้อมูลประมูล</h4>
        <div class="mb-3">
            <label>ราคาตั้งต้น</label>
            <input type="number" name="start_price" class="form-control" step="0.01" placeholder="กรอกราคาตั้งต้น" required>
        </div>

        <button type="submit" name="submit" class="btn btn-primary">เพิ่มข้อมูล</button>
    </form>
</div>

<script>
function previewImage(event){
    var reader = new FileReader();
    reader.onload = function(){
        var output = document.getElementById('preview');
        output.src = reader.result;
        output.style.display = 'block';
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
