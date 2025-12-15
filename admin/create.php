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

    // Xử lý upload ảnh
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../uploads/";

        if (!is_dir($targetDir))
            mkdir($targetDir, 0755, true);

        $fileName = basename($_FILES['image']['name']); // uploads/12345_index.php

        $targetFile = $targetDir . time() . "_" . $fileName;

        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowTypes = ['jpg', 'jpeg', 'png'];

        if (in_array($fileType, $allowTypes)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                // NOTE: QUAN TRỌNG - Đường dẫn LƯU VÀO DATABASE
                // bỏ dấu "../" đi. Chỉ lưu "uploads/ten_anh.jpg"
                // Để khi hiển thị ở home.php, nó đường dẫn đúng.
                $imagePath = "uploads/" . time() . "_" . $fileName;
            }
        }
    }

    // Thêm sản phẩm (Dùng Prepared Statement)
    $query = "INSERT INTO products (name, price, status, image, created_at) 
              VALUES (?, ?, ?, ?, NOW())";

    $stmt = mysqli_prepare($con, $query);
    // 'sdss' = string, double, string, string
    mysqli_stmt_bind_param($stmt, "sdss", $name, $price, $status, $imagePath);

    if (mysqli_stmt_execute($stmt)) {
        // Thêm thành công
        mysqli_stmt_close($stmt); // DỌN DẸP TRƯỚC
        mysqli_close($con); // DỌN DẸP TRƯỚC

        header("location: ../home.php");
        exit();
    } else {
        $errorMessage = "Fail to add product: " . mysqli_error($con);
        mysqli_stmt_close($stmt);
    }
}
mysqli_close($con);
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