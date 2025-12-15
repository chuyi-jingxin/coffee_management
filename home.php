<?php
session_start();
require_once 'config/db.php';

/* AUTO LOGIN */
// 1. Kiểm tra: User CHƯA đăng nhập (chưa có session) VÀ có cookie "remember_me"?
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

        // 4. "Đăng nhập" bằng cách tạo session
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
    }

    mysqli_stmt_close($stmt_find);
}
//

/* CHECK LOGIN */
if (!isset($_SESSION['username'])) {
    header('location:auth/login.php');
    exit();
}

/* TÍNH GIỎ HÀNG (Customer) */
$cart_count = 0;
if (isset($_SESSION['role']) && $_SESSION['role'] != 'admin') {
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $cart_count += $item['quantity'];
        }
    }
}

/* LẤY DANH SÁCH SẢN PHẨM */
$query = "SELECT * FROM products ORDER BY id DESC";
$result = mysqli_query($con, $query); // Object - False
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
</body>
</html>