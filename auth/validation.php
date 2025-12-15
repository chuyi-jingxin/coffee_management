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
    if (password_verify($pass, $row['password'])) {
        // Mật khẩu ĐÚNG
        $_SESSION['username'] = $row['username']; // login action
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['role'] = $row['role'];

        // ===== TOKEN =====
        // 1. Kiểm tra người dùng có tick "Remember me" 
        if (isset($_POST['remember_me'])) {

            // 2. Tạo token ngẫu nhiên, an toàn (64 ký tự - 32 byte)
            $token = bin2hex(random_bytes(32));

            // 3. Đặt thời gian hết hạn 
            $expires_in = 60 * 60 * 24 * 30; // 30 ngày (tính bằng giây)
            $expires_at = date('Y-m-d H:i:s', time() + $expires_in);
            $user_id = $row['id']; // id của users; liên kết token với user vừa nhập

            // 4. Lưu token vào DATABASE
            $stmt_token = mysqli_prepare($con, "INSERT INTO auth_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt_token, "iss", $user_id, $token, $expires_at);
            mysqli_stmt_execute($stmt_token);
            mysqli_stmt_close($stmt_token);

            // 5. Gửi cookie cho trình duyệt (httpOnly: Rất quan trọng, ngăn JS đọc)
            setcookie('remember_me', $token, time() + $expires_in, "/", "", false, true); 
        }
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