<?php
session_start();
require_once '../config/db.php';

$user = $_POST['user'];
$pass = $_POST['password'];

// 1. KIỂM TRA USERNAME (Prepared Statement)
$s = "SELECT id FROM users WHERE username = ?"; 
$stmt = mysqli_prepare($con, $s); // tạo khuôn mẫu câu lệnh SQL
mysqli_stmt_bind_param($stmt, 's', $user); 
mysqli_stmt_execute($stmt); // call db
mysqli_stmt_store_result($stmt); 

if (mysqli_stmt_num_rows($stmt) > 0) {
    // Username đã tồn tại

    mysqli_stmt_close($stmt); 
    mysqli_close($con);

    header('location:login.php?reg_error=exists');
    exit(); // RẤT QUAN TRỌNG
} else {
    // 2. BĂM MẬT KHẨU (An toàn)
    $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
    // BCRYPT

    // 3. THÊM USER MỚI (Prepared Statement)
    // $reg = "INSERT INTO users(username,password) VALUES (?, ?)";

    // Gán quyền mặc định là customer
    $role = 'customer';
    $reg = "INSERT INTO users(username, password, role) VALUES (?, ?, ?)";
    $stmt_insert = mysqli_prepare($con, $reg);
    mysqli_stmt_bind_param($stmt_insert, 'sss', $user, $hashed_password, $role);

    if (mysqli_stmt_execute($stmt_insert)) {
        // Đăng kí thành công
        mysqli_stmt_close($stmt_insert);
        mysqli_stmt_close($stmt);
        mysqli_close($con);

        header("location:login.php?success=true");
        exit(); 
    } else {
        // Đăng kí thất bại
        mysqli_stmt_close($stmt_insert);
        mysqli_stmt_close($stmt);
        mysqli_close($con);

        header("location:login.php?reg_error=fail");
        exit(); 
    }

}

?>