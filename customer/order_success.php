<?php
session_start();
// Auto login check nếu cần (tùy chọn)
$order_id = $_GET['orderid'] ?? 0;
?>

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

        
    </style>
</head>

<body>
    <div class="container">

        
    </div>
</body>

</html>