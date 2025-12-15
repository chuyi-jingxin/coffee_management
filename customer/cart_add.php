<?php
session_start();
require_once '../config/db.php';
// Logic khi add vào cart
// Kiểm tra đăng nhập
if (!isset($_SESSION['username'])) {
    header('location:../auth/login.php');
    exit();
}

