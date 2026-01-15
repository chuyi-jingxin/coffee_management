<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['username']) && isset($_COOKIE['remember_me'])) {
    $token = $_COOKIE['remember_me'];

    $stmt_find = mysqli_prepare(
        $con,
        "SELECT users.* FROM auth_tokens 
         JOIN users ON auth_tokens.user_id = users.id 
         WHERE auth_tokens.token = ? AND auth_tokens.expires_at > NOW()"
    );

    mysqli_stmt_bind_param($stmt_find, "s", $token);
    mysqli_stmt_execute($stmt_find);
    $result_find = mysqli_stmt_get_result($stmt_find);

    if ($user = mysqli_fetch_assoc($result_find)) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
    }

    mysqli_stmt_close($stmt_find);
}

if (!isset($_SESSION['username'])) {
    header('location:../auth/login.php');
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    header('location:../home.php?msg=no_permission');
    exit();
}

$errorMessage = '';

/* XỬ LÝ THÊM SẢN PHẨM */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $price = (float) $_POST['price'];
    $status = trim($_POST['status']);
    $imagePath = '';

    // Xử lý upload ảnh
    if (!empty($_FILES['image']['name'])) { //biến siêu toàn cục chuyên chứa dữ liệu file
        $targetDir = "../uploads/";

        if (!is_dir($targetDir))
            mkdir($targetDir, 0755, true); // nếu thư mục chưa có thì tự tạo

        $fileName = basename($_FILES['image']['name']); // mục đích đặt tên file chống trùng lặp
        $targetFile = $targetDir . time() . "_" . $fileName;

        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowTypes = ['jpg', 'jpeg', 'png']; // chỉ chấp nhận đuôi file như này

        if (in_array($fileType, $allowTypes)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                // Khi upload, file nằm tạm ở tmp_name
                // Bỏ vào thư mục đã tạo
                // Khi lưu vào DB, đường dẫn tính từ thư mục gốc home.php thì gọi uploads/anh.jpg 
                $imagePath = "uploads/" . time() . "_" . $fileName;
            }
        }
    }

    // Thêm sản phẩm (Dùng Prepared Statement)
    $query = "INSERT INTO products (name, price, status, image, created_at) 
              VALUES (?, ?, ?, ?, NOW())";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "sdss", $name, $price, $status, $imagePath);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt); 
        mysqli_close($con); 
        header("location: ../home.php");
        exit();
    } else {
        $errorMessage = "Fail to add product: " . mysqli_error($con);
        mysqli_stmt_close($stmt);
    }
}
mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Product</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }

        .form-card {
            background: #fff;
            padding: 40px;
            border-radius: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            max-width: 600px;
            margin: 50px auto;
        }

        .form-control {
            border-radius: 50px;
            padding: 20px;
            border: 1px solid #eee;
            background: #fcfcfc;
        }

        .form-control:focus {
            border-color: #6c5ce7;
            box-shadow: none;
            background: #fff;
        }

        .btn-pill {
            border-radius: 50px;
            font-weight: 600;
            padding: 10px 30px;
        }

        h3 {
            color: #6c5ce7;
            font-weight: 700;
            text-align: center;
            margin-bottom: 30px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="form-card">
            <h3>Add New Product ☕</h3>

            <?php if ($errorMessage): ?>
                <div class="alert alert-danger rounded-pill text-center"><?= $errorMessage ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data"> 
                <div class="form-group">
                    <label class="ml-2 font-weight-bold">Name</label>
                    <input type="text" name="name" class="form-control" required placeholder="Ex: Cappuccino">
                </div>
                <div class="form-group">
                    <label class="ml-2 font-weight-bold">Price (VND)</label>
                    <input type="number" name="price" class="form-control" required min="0" step="1000"
                        placeholder="Ex: 50000">
                </div>
                <div class="form-group">
                    <label class="ml-2 font-weight-bold">Status</label>
                    <select name="status" class="form-control" style="height: auto;">
                        <option value="In Stock">In Stock</option>
                        <option value="Out of Stock">Out of Stock</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="ml-2 font-weight-bold">Image</label>
                    <input type="file" name="image" accept=".jpg,.jpeg,.png" class="form-control-file ml-2">
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-success btn-pill mr-2 shadow-sm">Add Product</button>
                    <a href="../home.php" class="btn btn-outline-secondary btn-pill">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>