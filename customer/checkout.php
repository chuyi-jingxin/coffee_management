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
// THU HOÀI
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }

        .checkout-card {
            background: #fff;
            padding: 30px;
            margin-top: 40px;
            border-radius: 25px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }

        .form-control {
            border-radius: 50px;
            padding: 20px;
            background: #fcfcfc;
            border: 1px solid #eee;
        }

        .form-control:focus {
            background: #fff;
            box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.1);
            border-color: #6c5ce7;
        }

        .btn-pill {
            border-radius: 50px;
            font-weight: 600;
            padding: 12px;
        }
        
        .list-group-item {
            border: none;
            padding: 15px 0;
            border-bottom: 1px solid #f1f1f1;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="checkout-card">
            <h2 class="mb-4 font-weight-bold" style="color: #6c5ce7;">Checkout</h2>

        <div class="row">
            <div class="col-md-6 order-md-2 mb-4">
                <h4 class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Your Cart</span>
                        <span class="badge badge-secondary badge-pill"><?= count($cart) ?></span>
                </h4>
                <ul class="list-group mb-3">
                        <?php foreach ($cart as $item): ?>
                            <li class="list-group-item d-flex justify-content-between lh-condensed">
                                <div>
                                    <h6 class="my-0 font-weight-bold"><?= htmlspecialchars($item['name']) ?></h6>
                                    <small class="text-muted">Qty: <?= $item['quantity'] ?></small>
                                </div>
                                <span
                                    class="text-muted"><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?></span>
                            </li>
                    <?php endforeach; ?>

                    <li class="list-group-item d-flex justify-content-between bg-light rounded mt-2 p-3">
                            <span class="text-success font-weight-bold">Total (VND)</span>
                            <strong class="text-success"
                                style="font-size: 1.2rem;"><?= number_format($grand_total, 0, ',', '.') ?></strong>
                    </li>
                </ul>
            </div>

            <div class="col-md-6 order-md-1">
                <h4 class="mb-3">Delivery Address</h4>

                <form action="place_order.php" method="POST">
                    <div class="form-group">
                            <label class="ml-2 font-weight-bold text-muted">Full Name</label>
                            <input type="text" name="customer_name" class="form-control" required
                                placeholder="Ex: Nguyen Van A">
                    </div>

                    <div class="form-group">
                            <label class="ml-2 font-weight-bold text-muted">Phone Number</label>
                            <input type="text" name="phone" class="form-control" required placeholder="Ex: 0912345678">
                    </div>

                    <div class="form-group">
                            <label class="ml-2 font-weight-bold text-muted">Address</label>
                            <textarea name="address" class="form-control" rows="3" style="border-radius: 20px;" required
                                placeholder="Ex: 144 Xuan Thuy, Cau Giay..."></textarea>
                    </div>

                    <hr class="mb-4">
                    <button type="submit" class="btn btn-success btn-lg btn-block">Confirm Order ✅</button>
                    <a href="cart.php" class="btn btn-secondary btn-block">Back to Cart</a>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
