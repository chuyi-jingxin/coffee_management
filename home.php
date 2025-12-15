<?php
session_start();
require_once 'config/db.php';

/* AUTO LOGIN */
// 1. Kiểm tra: User CHƯA đăng nhập (chưa có session) VÀ có cookie "remember_me"?
if (!isset($_SESSION['username']) && isset($_COOKIE['remember_me'])) {
    $token = $_COOKIE['remember_me'];

    // 2. Tìm token trong CSDL VÀ token còn hạn
    $stmt_find = mysqli_prepare(
        $con,
        "SELECT users.* FROM auth_tokens 
         JOIN users ON auth_tokens.user_id = users.id 
         WHERE auth_tokens.token = ? AND auth_tokens.expires_at > NOW()"
    );
    mysqli_stmt_bind_param($stmt_find, "s", $token);
    mysqli_stmt_execute($stmt_find);
    $result_find = mysqli_stmt_get_result($stmt_find);

    // 3. Nếu tìm thấy token hợp lệ
    if ($user = mysqli_fetch_assoc($result_find)) {

        // 4. "Đăng nhập" bằng cách tạo session
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
    }

    mysqli_stmt_close($stmt_find);
}
//

/* CHECK LOGIN */
if (!isset($_SESSION['username'])) {
    header('location:auth/login.php');
    exit();
}

/* TÍNH GIỎ HÀNG (Customer) */
$cart_count = 0;
if (isset($_SESSION['role']) && $_SESSION['role'] != 'admin') {
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $cart_count += $item['quantity'];
        }
    }
}

/* LẤY DANH SÁCH SẢN PHẨM */
$query = "SELECT * FROM products ORDER BY id DESC";
$result = mysqli_query($con, $query); // Object - False
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee House - Menu</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
            color: #555;
        }

        /* --- NAVBAR --- */
        .navbar-custom {
            background-color: #fff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            /* Bóng đổ siêu nhẹ */
            padding: 15px 0;
        }

        .brand-text {
            font-weight: 700;
            color: #6c5ce7;
            /* Tím pastel đậm */
            font-size: 1.6rem;
            letter-spacing: -0.5px;
        }

        /* Card Sản phẩm đẹp */
        .product-card {
            border: none;
            border-radius: 25px;
            /* Bo góc nhiều hơn */
            background: #fff;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            /* Hiệu ứng nảy */
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 35px -5px rgba(0, 0, 0, 0.1);
        }

        .card-img-top {
            height: 220px;
            object-fit: cover;
            border-bottom: 1px solid #f0f0f0;
        }

        .card-body {
            padding: 25px;
            display: flex;
            flex-direction: column;
        }

        .product-title {
            font-weight: 600;
            font-size: 1.15rem;
            color: #2d3436;
            margin-bottom: 8px;
        }

        .product-price {
            color: #fdcb6e;
            /* Vàng nghệ Pastel */
            font-weight: 700;
            font-size: 1.3rem;
        }

        /* --- BADGES (Nhãn trạng thái) --- */
        .status-badge {
            position: absolute;
            top: 20px;
            left: 20px;
            /* Đổi sang trái cho lạ mắt */
            padding: 6px 15px;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .badge-instock {
            background-color: #00b894;
            color: white;
        }

        /* Xanh Mint đậm */
        .badge-outstock {
            background-color: #ff7675;
            color: white;
        }

        /* Hồng cam */

        /* --- CÁC NÚT BẤM (PASTEL STYLE) --- */
        /* Nút chung */
        .btn {
            border-radius: 50px;
            /* Bo tròn viên thuốc */
            font-weight: 600;
            border: none;
            padding: 8px 15px;
            transition: 0.3s;
        }

        /* Nút View Details (Khách) */
        .btn-pastel-view {
            background-color: #81ecec;
            /* Xanh ngọc nhạt */
            color: #00897b;
            /* Chữ xanh đậm */
            width: 100%;
            margin-top: auto;
        }

        .btn-pastel-view:hover {
            background-color: #4dd0e1;
            color: #fff;
            transform: scale(1.02);
        }

        /* Nút Edit (Admin) */
        .btn-pastel-edit {
            background-color: #a29bfe;
            /* Tím nhạt */
            color: #fff;
        }

        .btn-pastel-edit:hover {
            background-color: #6c5ce7;
        }

        /* Nút Delete (Admin) */
        .btn-pastel-del {
            background-color: #ff7675;
            /* Hồng đỏ nhạt */
            color: #fff;
        }

        .btn-pastel-del:hover {
            background-color: #d63031;
        }

        /* Nút Cart trên Header */
        .btn-pastel-cart {
            background-color: #ffeaa7;
            /* Vàng kem */
            color: #d35400;
        }

        .btn-pastel-cart:hover {
            background-color: #fdcb6e;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand brand-text" href="#">
                <i class="fas fa-mug-hot mr-2"></i>Coffee House
            </a>

            <div class="ml-auto d-flex align-items-center">
                <span class="mr-3 d-none d-md-inline text-muted">
                    Welcome, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>
                </span>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] != 'admin'): ?>
                    <a href="customer/cart.php" class="btn btn-pastel-cart mr-3 shadow-sm">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="badge badge-light text-dark ml-1"><?= $cart_count ?></span>
                    </a>
                <?php endif; ?>

                <a href="auth/logout.php" class="btn btn-outline-secondary btn-sm px-3" style="border-radius: 20px;">
                    Logout <i class="fas fa-sign-out-alt ml-1"></i>
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-5">

        <?php if (isset($_GET['msg'])): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <?php
                    $alertClass = ($_GET['msg'] == 'deleted' || $_GET['msg'] == 'updated') ? 'alert-success' : 'alert-danger';
                    $msgText = 'Action completed.';
                    if ($_GET['msg'] == 'deleted')
                        $msgText = 'Item deleted successfully!';
                    if ($_GET['msg'] == 'error')
                        $msgText = 'Something went wrong.';
                    if ($_GET['msg'] == 'no_permission') {
                        $msgText = 'Access Denied!';
                        $alertClass = 'alert-warning';
                    }
                    ?>
                    <div class="alert <?= $alertClass ?> alert-dismissible fade show shadow-sm"
                        style="border-radius: 15px;">
                        <?= $msgText ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h3 style="font-weight: 700; color: #2d3436;">Menu List ☕</h3>
                <p class="text-muted mb-0">Choose your favorite drink</p>
            </div>

            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                <div class="btn-group shadow-sm" style="border-radius: 50px; overflow: hidden;">
                    <a href="admin/manage_orders.php" class="btn btn-info px-3">Orders</a>
                    <a href="admin/manage_users.php" class="btn btn-primary px-3">Users</a>
                    <a href="admin/create.php" class="btn btn-success px-3">New Product</a>
                </div>
            <?php endif; ?>
        </div>

        <div class="row">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>

                    <div class="col-lg-3 col-md-4 col-sm-6 mb-5">
                        <div class="product-card h-100 position-relative">

                            <span class="status-badge <?= $row['status'] == 'In Stock' ? 'badge-instock' : 'badge-outstock' ?>">
                                <?= $row['status'] ?>
                            </span>

                            <img src="<?= htmlspecialchars($row['image'] ?: 'https://placehold.co/300x300?text=No+Image') ?>"
                                class="card-img-top" alt="Product Image">

                            <div class="card-body">
                                <h5 class="product-title"><?= htmlspecialchars($row['name']) ?></h5>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="product-price"><?= number_format($row['price'], 0, ',', '.') ?>đ</span>
                                </div>

                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                                    <div class="d-flex justify-content-between mt-auto">
                                        <a href="admin/edit.php?id=<?= $row['id'] ?>"
                                            class="btn btn-sm btn-pastel-edit flex-fill mr-2 shadow-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="admin/delete.php?id=<?= $row['id'] ?>"
                                            class="btn btn-sm btn-pastel-del flex-fill ml-2 shadow-sm"
                                            onclick="return confirm('Delete this product?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <a href="customer/product_detail.php?id=<?= $row['id'] ?>"
                                        class="btn btn-pastel-view shadow-sm">
                                        View Details <i class="fas fa-arrow-right ml-2"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-coffee fa-3x text-muted mb-3" style="opacity: 0.3"></i>
                    <h4 class="text-muted">Currently, the menu is empty.</h4>
                </div>
            <?php endif; ?>
        </div>
        <?php mysqli_close($con); ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        if (window.location.search.length >= 0) {
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    </script>

</body>

</html>