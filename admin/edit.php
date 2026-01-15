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
    header('Location: ../auth/login.php');
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    header('location:../home.php?msg=no_permission');
    exit();
}

// Lấy ID sản phẩm và validate
$id = (int) ($_GET['id'] ?? 0); // lấy id từ url, ép kiểu để bảo mật
if ($id <= 0) {
    mysqli_close($con); 
    die("Invalid product ID.");
}

// Lấy dữ liệu hiện tại (Dùng Prepared Statement)
$stmt_select = mysqli_prepare($con, "SELECT * FROM products WHERE id = ?"); // prepare
mysqli_stmt_bind_param($stmt_select, "i", $id);
mysqli_stmt_execute($stmt_select);
$result = mysqli_stmt_get_result($stmt_select);
$product = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt_select);

if (!$product) {
    mysqli_close($con); 
    die("Product not found.");
}

// Cập nhật sản phẩm
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $price = (float) $_POST['price'];
    $status = trim($_POST['status']);
    $image = $product['image']; // Giữ ảnh cũ nếu không upload mới

    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../uploads/";

        if (!is_dir($targetDir))
            mkdir($targetDir, 0755, true);

        $fileName = basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . time() . '_' . $fileName;

        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png'];

        if (in_array($fileType, $allowed) && move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $image = "uploads/" . time() . '_' . $fileName;
        }
    }

    // Cập nhật (Dùng Prepared Statement)
    $sql_update = "UPDATE products SET name=?, price=?, status=?, image=? WHERE id=?";
    $stmt_update = mysqli_prepare($con, $sql_update);
    mysqli_stmt_bind_param($stmt_update, "sdssi", $name, $price, $status, $image, $id);

    if (mysqli_stmt_execute($stmt_update)) {
        mysqli_stmt_close($stmt_update); 
        mysqli_close($con);            
        header("Location: ../home.php");
        exit();
    } else {
        echo "Lỗi: " . mysqli_error($con);
        mysqli_stmt_close($stmt_update); 
    }
}

// Dòng này chỉ chạy khi vào trang (GET) hoặc khi POST bị lỗi
mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
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

        img.current-img {
            border-radius: 15px;
            margin-top: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
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
            <h3>Edit Product ✏️</h3>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="ml-2 font-weight-bold">Name</label>
                    <input type="text" name="name" class="form-control" required
                        value="<?= htmlspecialchars($product['name']) ?>">
                </div>
                <div class="form-group">
                    <label class="ml-2 font-weight-bold">Price</label>
                    <input type="number" name="price" class="form-control" min="0" step="1000" required
                        value="<?= htmlspecialchars($product['price']) ?>">
                </div>
                <div class="form-group">
                    <label class="ml-2 font-weight-bold">Status</label>
                    <select name="status" class="form-control" style="height: auto;">
                        <option value="In Stock" <?= $product['status'] == 'In Stock' ? 'selected' : '' ?>>In Stock
                        </option>
                        <option value="Out of Stock" <?= $product['status'] == 'Out of Stock' ? 'selected' : '' ?>>Out of
                            Stock</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="ml-2 font-weight-bold">Image</label><br>
                    <input type="file" name="image" accept=".jpg,.jpeg,.png" class="ml-2">
                    <div class="mt-2 text-center">
                        <?php if ($product['image']): ?>
                            <img class="current-img" src="../<?= htmlspecialchars($product['image']) ?>" alt="Current Image"
                                width="100">
                        <?php endif; ?>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-pill mr-2 shadow-sm">Save Changes</button>
                    <a href="../home.php" class="btn btn-outline-secondary btn-pill">Back</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>