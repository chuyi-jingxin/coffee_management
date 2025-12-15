<?php
session_start();
// NOTE: Trang này chủ yếu lấy dữ liệu từ Session, không cần DB cũng được, 
// nhưng cứ include để dùng Auto Login nếu cần sau này.
require_once '../config/db.php';
// giao diện cart
// Kiểm tra đăng nhập
if (!isset($_SESSION['username'])) {
    header('location:../auth/login.php');
    exit();
}

// Xử lý Xóa sản phẩm khỏi giỏ (Nếu bấm nút Delete)
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])) {
    $remove_id = (int)$_GET['id'];
    unset($_SESSION['cart'][$remove_id]); // Xóa khỏi session
    header('location: cart.php'); // Load lại trang
    exit();
}

$cart = $_SESSION['cart'] ?? [];
$grand_total = 0;
?>

