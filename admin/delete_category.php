<?php
require_once "../config/database.php";
require_once "../models/category.php";
require_once "../public/session.php"; // Quản lý phiên

checkAdmin(); // Kiểm tra xem người dùng có phải là admin hay không

if (!isset($_GET['id'])) {
    header("Location: manage_category.php");
    exit();
}

$categoryId = $_GET['id'];
$categoryModel = new category($conn);
$categoryModel->deleteCategory($categoryId);

header("Location: manage_category.php");
exit();
