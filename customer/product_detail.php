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

// LẤY ID SẢN PHẨM
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$id = (int) ($_GET['id'] ?? 0);

// TRUY VẤN SẢN PHẨM
$stmt = mysqli_prepare($con, "SELECT * FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    die("The product does not exist!");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['name']) ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }

        .product-container {
            background: #fff;
            padding: 40px;
            margin-top: 50px;
            border-radius: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
        }

        .detail-img {
            width: 100%;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            object-fit: cover;
        }

        .price-tag {
            color: #fdcb6e;
            font-size: 2.5rem;
            font-weight: 700;
        }

        .btn-pill {
            border-radius: 50px;
            font-weight: 600;
            padding: 12px 0;
        }

        .form-control-qty {
            border-radius: 50px;
            text-align: center;
            border: 2px solid #eee;
            height: 50px;
            font-size: 1.2rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="product-container">
            <div class="row align-items-center">
                <div class="col-md-6 mb-4 mb-md-0">
                    <img src="../<?= htmlspecialchars($product['image'] ?: 'assets/img/no-image.png') ?>"
                        class="detail-img" alt="Product Image">
                </div>

                <div class="col-md-6 pl-md-5">
                    <h2 class="font-weight-bold mb-3"><?= htmlspecialchars($product['name']) ?></h2>
                    <p class="price-tag"><?= number_format($product['price'], 0, ',', '.') ?> <small
                            style="font-size: 1rem; color: #aaa;">VND</small></p>

                    <div class="mb-4">
                        <span
                            class="badge badge-<?= $product['status'] == 'In Stock' ? 'success' : 'secondary' ?> p-2 rounded-pill px-3">
                            <?= $product['status'] ?>
                        </span>
                    </div>

                    <p class="text-muted">Enjoy the finest taste of our coffee, brewed to perfection just for you.</p>
                    <hr class="my-4">

                    <form action="cart_add.php" method="POST">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-muted">Quantity</label>
                            <input type="number" name="quantity" value="1" min="1" max="10"
                                class="form-control form-control-qty w-50">
                        </div>

                        <?php if ($product['status'] == 'In Stock'): ?>
                            <button type="submit" class="btn btn-warning btn-pill btn-block text-white shadow-sm"
                                style="background-color: #fdcb6e; border: none;">
                                Add to Cart <i class="fas fa-shopping-cart ml-2"></i>
                            </button>
                        <?php else: ?>
                            <button type="button" class="btn btn-secondary btn-pill btn-block" disabled>Sold Out</button>
                        <?php endif; ?>
                    </form>

                    <a href="../home.php" class="btn btn-outline-secondary btn-pill btn-block mt-3">Back to Menu</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>