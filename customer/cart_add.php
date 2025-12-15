<?php
session_start();
require_once '../config/db.php';
// Logic khi add vào cart
// Kiểm tra đăng nhập
if (!isset($_SESSION['username'])) {
    header('location:../auth/login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Lấy dữ liệu từ form
    $id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

    // 2. Validate
    if ($id <= 0 || $quantity <= 0) {
        die("Invalid product or quantity");
    }

    // 3. Lấy thông tin sản phẩm chuẩn từ DB (Để tránh hack giá từ HTML)
    $stmt = mysqli_prepare($con, "SELECT * FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);

    if ($product) {
        // 4. Logic Giỏ hàng (Session)
        
        // Nếu giỏ hàng chưa tồn tại, tạo mới
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        

        
    } else {
        
    }
}
?>