<?php
session_start();

// ===== BẮT ĐẦU =====
// 1. Hủy cookie "remember_me" trong CSDL 
if (isset($_COOKIE['remember_me'])) {
    require_once '../config/db.php';
    $token = $_COOKIE['remember_me'];
    
    $stmt_delete = mysqli_prepare($con, "DELETE FROM auth_tokens WHERE token = ?");
    mysqli_stmt_bind_param($stmt_delete, "s", $token);
    mysqli_stmt_execute($stmt_delete);
    
    mysqli_stmt_close($stmt_delete);
    mysqli_close($con);
}

// 2. Hủy cookie "remember_me" khỏi trình duyệt
// (Bằng cách đặt thời gian hết hạn là 1 giờ trong quá khứ)
setcookie('remember_me', '', time() - 3600, "/");
// ===== KẾT THÚC =====

session_destroy(); // hủy cả $_SESSION và Cookie
header('location:login.php');

exit();
?>
