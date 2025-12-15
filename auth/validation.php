<?php
session_start(); 
require_once '../config/db.php';

$user = $_POST['user'];
$pass = $_POST['password'];

// 1. TÌM USER TRONG DATABASE (Prepared Statement)
$s = "SELECT * FROM users WHERE username = ?"; 
$stmt = mysqli_prepare($con, $s);
mysqli_stmt_bind_param($stmt, 's', $user);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// 2. KIỂM TRA KẾT QUẢ
if ($row = mysqli_fetch_assoc($result)) { // ['id'=>3, 'username'=>'chuyi',
    // Tìm thấy user, TIẾP TỤC KIỂM TRA MẬT KHẨU

    // 3. XÁC THỰC MẬT KHẨU (An toàn)
    // Bây giờ $row['password'] đã tồn tại vì dùng SELECT *

        // ===== KẾT THÚC =====

        mysqli_stmt_close($stmt);
        mysqli_close($con);

        header('location:../home.php');
        exit(); 

    } else {
        // Mật khẩu SAI
        mysqli_stmt_close($stmt);
        mysqli_close($con);

        header('location:login.php?error=invalid');
        exit(); 
    }
} else {
    // Không tìm thấy user
    mysqli_stmt_close($stmt);
    mysqli_close($con);

    header('location:login.php?error=nouser');
    exit();
}

?>