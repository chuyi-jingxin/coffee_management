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
            font-family: 'Poppins', sans-serif;
        }

        .detail-card {
            background: #fff;
            border-radius: 25px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            padding: 25px;
            height: 100%;
            border: none;
        }

        .detail-header {
            border-bottom: 2px solid #f1f2f6;
            padding-bottom: 15px;
            margin-bottom: 20px;
            font-weight: 700;
            color: #6c5ce7;
        }

        .thumb-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 15px;
        }
        .btn-pill {
            border-radius: 50px;
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 style="font-weight: 700;">Order Details <span class="text-muted">#<?= $order_id ?></span></h3>
            <a href="manage_orders.php" class="btn btn-secondary btn-pill px-4"><i class="fas fa-arrow-left"></i>
                Back</a>
        </div>

        <div class="row">
            <div class="col-md-5 mb-4">
                <div class="detail-card">
                    <h5 class="detail-header"><i class="fas fa-user-circle mr-2"></i> Customer Info</h5>
                    <p><strong>Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?></p>
                    <p><strong>Address:</strong> <?= htmlspecialchars($order['address']) ?></p>
                    <p><strong>Date:</strong> <?= $order['created_at'] ?></p>
                    <p><strong>Status:</strong> <span
                            class="badge badge-info p-2 rounded"><?= $order['status'] ?></span></p>
                </div>
            </div>

            <div class="col-md-7">
                <div class="detail-card">
                    <h5 class="detail-header"><i class="fas fa-shopping-basket mr-2"></i> Items List</h5>
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead class="text-muted border-bottom">
                                <tr>
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
                                            <div class="d-flex align-items-center">
                                                <img src="../<?= htmlspecialchars($item['image'] ?: 'assets/img/no-image.png') ?>"
                                                    class="thumb-img mr-3">
                                                <span><?= htmlspecialchars($item['product_name']) ?></span>
                                            </div>
                                        </td>
                                        <td class="align-middle">x<?= $item['quantity'] ?></td>
                                        <td class="align-middle"><?= number_format($item['price']) ?></td>
                                        <td class="align-middle font-weight-bold">
                                            <?= number_format($item['price'] * $item['quantity']) ?></td>
                                    </tr>
                                <?php endwhile; ?>

                        <tr class="border-top">
                                    <td colspan="3" class="text-right"><strong>Grand Total:</strong></td>
                                    <td class="text-danger font-weight-bold" style="font-size: 1.2rem;">
                                        <?= number_format($order['total_amount'], 0, ',', '.') ?> VND
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        </div>
</body>
</html>