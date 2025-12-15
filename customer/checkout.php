<?php
session_start();
require_once '../config/db.php';
// Giao diện điền thông tin
// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['username'])) {
    header('location:../auth/login.php');
    exit();
}

// 2. Kiểm tra giỏ hàng có trống không?
$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header('location: cart.php'); // Nếu rỗng thì đuổi về trang giỏ hàng
    exit();
}

// 3. Tính tổng tiền để hiển thị
$grand_total = 0;
foreach ($cart as $item) {
    $grand_total += $item['price'] * $item['quantity'];
}
?>
// Code in here (Thu Hoai)
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            background: #f8f9fa;
        }

        .container {
            background: #fff;
            padding: 30px;
            margin-top: 50px;
            border-radius: 8px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="mb-4">Checkout 📝</h2>

        <div class="row">
            <div class="col-md-6">
                <h4 class="text-info">Order Summary</h4>
                <ul class="list-group mb-3">
                    <?php foreach ($cart as $item): ?>
                        <li class="list-group-item d-flex justify-content-between lh-condensed">
                            <div>
                                <h6 class="my-0"><?= htmlspecialchars($item['name']) ?></h6>
                                <small class="text-muted">Quantity: <?= $item['quantity'] ?></small>
                            </div>
                            <span class="text-muted">
                                <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>
                            </span>
                        </li>
                    <?php endforeach; ?>

                    <li class="list-group-item d-flex justify-content-between bg-light">
                        <span class="text-success font-weight-bold">Total (VND)</span>
                        <strong class="text-success"><?= number_format($grand_total, 0, ',', '.') ?></strong>
                    </li>
                </ul>
            </div>

            <div class="col-md-6">
                <h4 class="text-primary">Delivery Information</h4>

                <form action="place_order.php" method="POST">
                    <div class="form-group">
                        <label>Receiver Name:</label>
                        <input type="text" name="customer_name" class="form-control" required
                            placeholder="Enter your full name">
                    </div>

                    <div class="form-group">
                        <label>Phone Number:</label>
                        <input type="text" name="phone" class="form-control" required placeholder="09xxxxxxxx">
                    </div>

                    <div class="form-group">
                        <label>Shipping Address:</label>
                        <textarea name="address" class="form-control" rows="3" required
                            placeholder="Your delivery address..."></textarea>
                    </div>

                    
                </form>
            </div>
        </div>
    </div>
</body>

</html>