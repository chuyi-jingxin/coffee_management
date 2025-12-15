<?php
session_start();
require_once '../config/db.php';

// ===== BẮT ĐẦU =====
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

        // 4. "Đăng nhập" cho họ bằng cách tạo session
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
    }

    mysqli_stmt_close($stmt_find);
}
// ===== KẾT THÚC =====

// Kiểm tra đăng nhập
if (!isset($_SESSION['username'])) {
    header('Location: ../auth/login.php');
    exit();
}

// Chỉ cho phép ADMIN truy cập.
if ($_SESSION['role'] !== 'admin') {
    header('location:../home.php?msg=no_permission');
    exit();
}

// Lấy ID sản phẩm và validate
$id = (int) ($_GET['id'] ?? 0); // lấy id từ url, ép kiểu để bảo mật
if ($id <= 0) {
    mysqli_close($con); // Dọn dẹp $con
    die("Invalid product ID.");
}

// Lấy dữ liệu hiện tại (Dùng Prepared Statement)
$stmt_select = mysqli_prepare($con, "SELECT * FROM products WHERE id = ?"); // prepare
mysqli_stmt_bind_param($stmt_select, "i", $id);
mysqli_stmt_execute($stmt_select);
$result = mysqli_stmt_get_result($stmt_select);
$product = mysqli_fetch_assoc($result);//to array

// Dọn dẹp $stmt_select ngay sau khi dùng xong
mysqli_stmt_close($stmt_select);

if (!$product) {
    mysqli_close($con); // Dọn dẹp $con
    die("Product not found.");
}

// Cập nhật sản phẩm
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $price = (float) $_POST['price'];
    $status = trim($_POST['status']);
    $image = $product['image']; // Giữ ảnh cũ nếu không upload mới

    // Nếu có file upload
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../uploads/";

        if (!is_dir($targetDir))
            mkdir($targetDir, 0755, true);

        $fileName = basename($_FILES["image"]["name"]);

        // Đường dẫn vật lý: ../uploads/123_anh.jpg
        $targetFile = $targetDir . time() . '_' . $fileName;

        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png'];

        if (in_array($fileType, $allowed) && move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $image = "uploads/" . time() . '_' . $fileName;
        }
    }

    // Cập nhật (Dùng Prepared Statement)
    $sql_update = "UPDATE products SET name=?, price=?, status=?, image=? WHERE id=?";
    $stmt_update = mysqli_prepare($con, $sql_update);
    // 'sdssi' = string, double, string, string, integer
    mysqli_stmt_bind_param($stmt_update, "sdssi", $name, $price, $status, $image, $id);

    if (mysqli_stmt_execute($stmt_update)) {
        // Cập nhật thành công
        mysqli_stmt_close($stmt_update); // DỌN DẸP TRƯỚC
        mysqli_close($con);              // DỌN DẸP TRƯỚC

        header("Location: ../home.php");
        exit();
    } else {
        // Cập nhật thất bại
        echo "Lỗi: " . mysqli_error($con);
        mysqli_stmt_close($stmt_update); // Dọn dẹp $stmt_update
    }
}

// Dòng này chỉ chạy khi vào trang (GET) hoặc khi POST bị lỗi
mysqli_close($con);
?>