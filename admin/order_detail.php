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
// LY
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order Details #<?= $order_id ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
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
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Order Details 🧾 <small class="text-muted">#<?= $order_id ?></small></h3>
            <a href="manage_orders.php" class="btn btn-secondary">Back to List</a>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">Customer Info</div>
                    <div class="card-body">
                        <p><strong>Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
                        <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?></p>
                        <p><strong>Address:</strong> <?= htmlspecialchars($order['address']) ?></p>
                        <p><strong>Order Date:</strong> <?= $order['created_at'] ?></p>
                        <p><strong>Status:</strong> <span class="badge badge-primary"><?= $order['status'] ?></span></p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <h5 class="text-primary">Items List</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($item = mysqli_fetch_assoc($result_items)): ?>
                            <tr>
                                <td>
                                    <img src="../<?= htmlspecialchars($item['image'] ?: 'assets/img/no-image.png') ?>"
                                        class="thumb-img">
                                </td>
                                <td><?= htmlspecialchars($item['product_name']) ?></td>
                                <td>x<?= $item['quantity'] ?></td>
                                <td><?= number_format($item['price']) ?></td>
                                <td><?= number_format($item['price'] * $item['quantity']) ?></td>
                            </tr>
                        <?php endwhile; ?>

                        <tr class="bg-light">
                            <td colspan="4" class="text-right"><strong>Grand Total:</strong></td>
                            <td class="text-danger font-weight-bold">
                                <?= number_format($order['total_amount'], 0, ',', '.') ?> VND
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
