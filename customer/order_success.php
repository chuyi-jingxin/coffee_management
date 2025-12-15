<?php
session_start();
// Auto login check nếu cần (tùy chọn)
$order_id = $_GET['orderid'] ?? 0;
?>
// Code in here (Nhật)
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order Success</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            background: #f8f9fa;
        }

        .container {
            background: #fff;
            padding: 50px;
            margin-top: 50px;
            border-radius: 8px;
            text-align: center;
        }

        .success-icon {
            font-size: 80px;
            color: #28a745;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="success-icon">🎉</div>
        <h2 class="text-success mt-3">Order Placed Successfully!</h2>
        <p class="lead">Thank you for your purchase.</p>
        <p>Your Order ID is: <strong>#<?= htmlspecialchars($order_id) ?></strong></p>
        <hr>
        <p>We will contact you soon to confirm your order.</p>
        <a href="../home.php" class="btn btn-primary">Back to Home</a>
    </div>
</body>

</html>