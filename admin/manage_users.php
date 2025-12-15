<?php
session_start();
require_once '../config/db.php';

// ===== BẮT ĐẦU: AUTO LOGIN (TOKEN) =====
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

// NOTE: BẢO VỆ: CHỈ ADMIN MỚI ĐƯỢC VÀO
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('location:../home.php?msg=no_permission');
    exit();
}

/* 3. LẤY DANH SÁCH USER (Trừ chính mình) */
$my_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id != $my_id ORDER BY id DESC";
$result = mysqli_query($con, $query);

/* 4. XỬ LÝ XÓA USER */
if (isset($_GET['delete_id'])) {
    $del_id = (int) $_GET['delete_id'];
    mysqli_query($con, "DELETE FROM users WHERE id = $del_id");

    header("location: manage_users.php?msg=deleted");
    exit();
}
?>
// LY
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }
        .container {
            margin-top: 30px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
        }
    </style>
</head>

<body style="background: #f8f9fa;">
    <div class="container">
        <div class="d-flex justify-content-between mb-4">
            <h3>User Management 👥</h3>

            <a href="../home.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted')
            echo '<div class="alert alert-success">User deleted!</div>'; ?>

        <table class="table table-bordered text-center">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td>
                            <span class="badge badge-<?= $row['role'] == 'admin' ? 'danger' : 'info' ?>">
                                <?= $row['role'] ?>
                            </span>
                        </td>
                        <td>
                            <a href="manage_users.php?delete_id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                                onclick="return confirm('Delete this user?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>