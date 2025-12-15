<?php
session_start();
require_once '../config/db.php';
// Hiển thị danh sách tất cả đơn hàng (Mới nhất lên đầu).
// Xử lý nút bấm: Admin bấm "Complete" hoặc "Cancel" thì cập nhật ngay vào Database.

// 1. CHECK QUYỀN ADMIN
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('location:../home.php?msg=no_permission');
    exit();
}

// 2. XỬ LÝ CẬP NHẬT TRẠNG THÁI (Khi bấm nút ✔ hoặc ✘)
if (isset($_POST['update_status'])) {
    $order_id = (int) $_POST['order_id'];
    $new_status = $_POST['status']; // Nhận giá trị 'Completed' hoặc 'Cancelled'

    // NOTE: Cập nhật vào DB để khóa trạng thái đơn hàng
    $stmt = mysqli_prepare($con, "UPDATE orders SET status = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $new_status, $order_id);
    mysqli_stmt_execute($stmt);

    // Load lại trang để thấy nút bấm biến mất
    header("location: manage_orders.php?msg=updated");
    exit();
}

// 3. LẤY DANH SÁCH ĐƠN HÀNG (Mới nhất lên đầu)
$query = "SELECT * FROM orders ORDER BY created_at DESC";
$result = mysqli_query($con, $query);
?>
// Code in here (Ly)
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            background: #f8f9fa;
        }

        .container {
            background: #fff;
            padding: 20px;
            margin-top: 30px;
            border-radius: 8px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="d-flex justify-content-between mb-4">
            <h3>Order Management 📦</h3>
            <a href="../home.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'updated')
            echo '<div class="alert alert-success">Order status updated!</div>'; ?>

        <table class="table table-bordered text-center">
            <thead class="thead-dark">
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Total (VND)</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            
        </table>
    </div>
</body>

</html>