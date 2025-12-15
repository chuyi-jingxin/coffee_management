<?php
session_start();
require_once '../config/db.php';
// Logic xử lý khi thêm vào DB
// Kiểm tra đăng nhập
if (!isset($_SESSION['username'])) {
    header('location:../auth/login.php');
    exit();
}

// Kiểm tra giỏ hàng
$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    die("Cart is empty");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Lấy thông tin người nhận
    $user_id = $_SESSION['user_id'];
    $name = trim($_POST['customer_name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    
    // Tính lại tổng tiền (Backend phải tự tính lại để bảo mật, không tin client)
    $total_amount = 0;
    foreach ($cart as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }

    // 2. TẠO ĐƠN HÀNG (INSERT vào bảng orders)
    $stmt = mysqli_prepare($con, "INSERT INTO orders (user_id, customer_name, phone, address, total_amount, status, created_at) VALUES (?, ?, ?, ?, ?, 'Pending', NOW())");
    
    // 'isssd' -> integer, string, string, string, double
    mysqli_stmt_bind_param($stmt, "isssd", $user_id, $name, $phone, $address, $total_amount);
    
    if (mysqli_stmt_execute($stmt)) {
        // Lấy ID của đơn hàng vừa tạo (Order ID)
        $order_id = mysqli_insert_id($con);
        mysqli_stmt_close($stmt);

        // 3. LƯU CHI TIẾT ĐƠN HÀNG (INSERT vào bảng order_items)
        // Dùng vòng lặp để lưu từng món
        
        }
        

    } else {
        
    }
}
mysqli_close($con);
// Lưu Order => Lưu Items => xóa giỏ => Chuyển hướng
?>
