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

