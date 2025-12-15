<?php
session_start();
require_once '../config/db.php';
// Admin biết khách hàng order những món gì

// 1. CHECK QUYỀN
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('location:../home.php?msg=no_permission');
    exit();
}

$order_id = (int) ($_GET['id'] ?? 0);

// 2. LẤY THÔNG TIN ĐƠN HÀNG (Người mua, địa chỉ...)
$stmt = mysqli_prepare($con, "SELECT * FROM orders WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);
$order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$order)
    die("Order not found");

// 3. LẤY DANH SÁCH MÓN ĂN (Join với bảng products để lấy tên và ảnh)
$query_items = "SELECT oi.*, p.name as product_name, p.image 
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = $order_id";
$result_items = mysqli_query($con, $query_items);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order Details #<?= $order_id ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            background: #f8f9fa;
        }

        .container {
            background: #fff;
            padding: 30px;
            margin-top: 30px;
            border-radius: 8px;
        }

        .thumb-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }
    </style>
</head>

<body>

</body>

</html>