<?php
session_start();
require_once '../config/db.php';
// Hiển thị danh sách tất cả đơn hàng (Mới nhất lên đầu).
// Xử lý nút bấm: Admin bấm "Complete" hoặc "Cancel" thì cập nhật ngay vào Database.

// 1. CHECK QUYỀN ADMIN
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('location:../home.php?msg=no_permission');
    exit();
}

// 2. XỬ LÝ CẬP NHẬT TRẠNG THÁI (Khi bấm nút ✔ hoặc ✘)
if (isset($_POST['update_status'])) {
    $order_id = (int) $_POST['order_id'];
    $new_status = $_POST['status']; // Nhận giá trị 'Completed' hoặc 'Cancelled'

    // NOTE: Cập nhật vào DB để khóa trạng thái đơn hàng
    $stmt = mysqli_prepare($con, "UPDATE orders SET status = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $new_status, $order_id);
    mysqli_stmt_execute($stmt);

    // Load lại trang để thấy nút bấm biến mất
    header("location: manage_orders.php?msg=updated");
    exit();
}

// 3. LẤY DANH SÁCH ĐƠN HÀNG (Mới nhất lên đầu)
$query = "SELECT * FROM orders ORDER BY created_at DESC";
$result = mysqli_query($con, $query);
?>
// LY
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
         body {
            background: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }

        .main-card {
            background: #fff;
            padding: 30px;
            margin-top: 30px;
            border-radius: 25px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }

        h3 {
            color: #6c5ce7;
            font-weight: 700;
        }

        .table thead th {
            background-color: #6c5ce7;
            color: white;
            border: none;
        }

        .table-hover tbody tr:hover {
            background-color: #f1f2f6;
        }

        .btn-pill {
            border-radius: 50px;
            font-weight: 600;
            padding: 5px 15px;
        }

        .badge-pill-custom {
            padding: 8px 12px;
            border-radius: 20px;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="main-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3>Order Management</h3>
                <a href="../home.php" class="btn btn-secondary btn-pill"><i class="fas fa-arrow-left mr-1"></i> Dashboard</a>
            </div>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'updated')
            echo '<div class="alert alert-success">Order status updated!</div>'; ?>

        <div class="table-responsive">
                <table class="table table-hover text-center">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer Info</th>
                            <th>Total (VND)</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><strong>#<?= $row['id'] ?></strong></td>
                        <td class="text-left">
                            <?= htmlspecialchars($row['customer_name']) ?><br>
                            <small class="text-muted"><?= htmlspecialchars($row['phone']) ?></small>
                        </td>
                        <td class="text-danger font-weight-bold">
                            <?= number_format($row['total_amount'], 0, ',', '.') ?>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>

                        <td>
                            <?php
                            $badgeColor = 'secondary';
                            if ($row['status'] == 'Pending')
                                $badgeColor = 'warning';   // Màu vàng
                            if ($row['status'] == 'Completed')
                                $badgeColor = 'success'; // Màu xanh
                            if ($row['status'] == 'Cancelled')
                                $badgeColor = 'danger';  // Màu đỏ
                            ?>
                            <span class="badge badge-<?= $badgeColor ?> p-2"><?= $row['status'] ?></span>
                        </td>

                        <td>
                           <a href="order_detail.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info btn-pill">
                                        <i class="fas fa-eye"></i> View
                            </a>
                            <?php if ($row['status'] == 'Pending'): ?>
                                <span class="mx-1">|</span>

                                <form method="POST" style="display:inline-block;">
                                    <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="update_status" value="1">
                                    <button type="submit" name="status" value="Completed" class="btn btn-sm btn-success"
                                        onclick="return confirm('Are you sure to COMPLETE this order?');">✔</button>
                                </form>

                                <form method="POST" style="display:inline-block;">
                                    <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="update_status" value="1">
                                    <button type="submit" name="status" value="Cancelled" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure to CANCEL this order?');">✘</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>