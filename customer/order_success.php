<?php
session_start();
// Auto login check nếu cần (tùy chọn)
$order_id = $_GET['orderid'] ?? 0;
?>
// HOÀNG NHẬT
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order Success</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
    body {
            background: #f8f9fa;
            font-family: 'Poppins', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .success-card {
            background: #fff;
            padding: 50px;
            border-radius: 30px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }

        .success-icon {
            font-size: 80px;
            color: #00b894;
            margin-bottom: 20px;
        }

        .btn-pill {
            border-radius: 50px;
            font-weight: 600;
            padding: 12px 30px;
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
