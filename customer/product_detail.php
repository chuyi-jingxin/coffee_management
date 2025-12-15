<?php
session_start();
require_once '../config/db.php';

// AUTO LOGIN 
if (!isset($_SESSION['username']) && isset($_COOKIE['remember_me'])) {
    $token = $_COOKIE['remember_me'];
    $stmt_find = mysqli_prepare($con, 
        "SELECT users.* FROM auth_tokens 
         JOIN users ON auth_tokens.user_id = users.id 
         WHERE auth_tokens.token = ? AND auth_tokens.expires_at > NOW()");
    mysqli_stmt_bind_param($stmt_find, "s", $token);
    mysqli_stmt_execute($stmt_find);
    $result_find = mysqli_stmt_get_result($stmt_find);
    if ($user = mysqli_fetch_assoc($result_find)) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
    }
    mysqli_stmt_close($stmt_find);
}

// KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['username'])) {
    header('location:../auth/login.php');
    exit();
}

// LẤY ID SẢN PHẨM
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// TRUY VẤN SẢN PHẨM
$stmt = mysqli_prepare($con, "SELECT * FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    die("The product does not exist!");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['name']) ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body { background: #f8f9fa; }
        .container { background: #fff; padding: 30px; margin-top: 50px; border-radius: 8px; }
        .detail-img { 
            width: 100%; 
            max-width: 400px; 
            border-radius: 8px; 
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .price-tag { color: #d9534f; font-size: 24px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6 text-center">
                <img src="../<?= htmlspecialchars($product['image'] ?: 'assets/img/no-image.png') ?>" 
                     class="detail-img" alt="Product Image">
            </div>

            
        </div>
    </div>
</body>
</html>