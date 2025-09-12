<?php
session_start();
include("../config/connectdb.php");

$buyer_id   = $_SESSION['id']; 
$auction_id = $_POST['auction_id'];
$bid_amount = $_POST['bid_amount'];

// 1. ดึงข้อมูลการประมูล
$result = $conn->query("SELECT seller_id, current_price, start_price FROM auctions WHERE auction_id=$auction_id");
$auction = $result->fetch_assoc();

// 1a. ตรวจสอบว่าเป็น seller เองหรือไม่
$seller_id = $auction['seller_id'];
if ($buyer_id == $seller_id) {
    // แจ้งเตือนและพากลับหน้าโฮม
    echo "<script>
            alert('คุณไม่มีสิทธิ์เสนอราคาในสินค้าของตัวเอง!');
            window.location.href='home.php';
          </script>";
    exit;
}

// ตรวจสอบว่าราคาที่เสนอสูงกว่าปัจจุบัน
$current_price = $auction['current_price'] ?? $auction['start_price'];
if ($bid_amount <= $current_price) {
    die("❌ กรุณาใส่ราคาที่สูงกว่าราคาปัจจุบัน");
}

// 2. บันทึก bid
$stmt = $conn->prepare("INSERT INTO bids (auction_id, buyer_id, bid_amount) VALUES (?, ?, ?)");
$stmt->bind_param("iid", $auction_id, $buyer_id, $bid_amount);
$stmt->execute();

// 3. อัปเดตราคาล่าสุด
// 3. อัปเดตราคาล่าสุด
$conn->query("UPDATE auctions SET current_price=$bid_amount, last_bid_time=NOW() WHERE auction_id=$auction_id");


// 4. สร้างแจ้งเตือนให้ seller
$message = "มีการเสนอราคาใหม่ ฿$bid_amount ในการประมูลของคุณ";
$stmt = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
$stmt->bind_param("is", $seller_id, $message);
$stmt->execute();

// 5. กลับไปหน้า list
header("Location: home.php?success=1");
exit;
?>
