<?php
session_start();
require_once '../config/db.php';

// ===== BẮT ĐẦU =====
// 1. Kiểm tra: User CHƯA đăng nhập (chưa có session) 
//    VÀ có cookie "remember_me"?
if (!isset($_SESSION['username']) && isset($_COOKIE['remember_me'])) {

    $token = $_COOKIE['remember_me'];

    // 2. Tìm token trong CSDL VÀ token còn hạn
    $stmt_find = mysqli_prepare(
        $con,
        "SELECT users.* FROM auth_tokens 
         JOIN users ON auth_tokens.user_id = users.id 
         WHERE auth_tokens.token = ? AND auth_tokens.expires_at > NOW()"
    );

    mysqli_stmt_bind_param($stmt_find, "s", $token);
    mysqli_stmt_execute($stmt_find);
    $result_find = mysqli_stmt_get_result($stmt_find);

    // 3. Nếu tìm thấy token hợp lệ
    if ($user = mysqli_fetch_assoc($result_find)) {

        // 4. "Đăng nhập" cho họ bằng cách tạo session
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
    }

    mysqli_stmt_close($stmt_find);
}

/* KIỂM TRA ĐĂNG NHẬP */
if (!isset($_SESSION['username'])) {
    header('location:../auth/login.php');
    exit();
}

// Chỉ cho phép ADMIN truy cập.
if ($_SESSION['role'] !== 'admin') {
    header('location:../home.php?msg=no_permission');
    exit();
}

$errorMessage = '';

/* XỬ LÝ THÊM SẢN PHẨM */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $price = (float) $_POST['price'];
    $status = trim($_POST['status']);
    $imagePath = '';

} 