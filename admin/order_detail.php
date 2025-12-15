<?php
session_start();
require_once '../config/db.php';
// Admin biết khách hàng order những món gì

// 1. CHECK QUYỀN
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('location:../home.php?msg=no_permission');
    exit();
}

$order_id = (int)($_GET['id'] ?? 0);

