<?php
require_once "../config/database.php";
require_once "../models/Category.php";
require_once "../public/session.php"; // Quản lý phiên

checkAdmin(); // Kiểm tra xem người dùng có phải là admin hay không

if (!isset($_GET['id'])) {
    header("Location: manage_categories.php");
    exit();
}

$categoryId = $_GET['id'];
$categoryModel = new Category($conn);
$categoryModel->deleteCategory($categoryId);

header("Location: manage_categories.php");
exit();
