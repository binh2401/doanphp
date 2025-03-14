<?php
session_start();
require_once "models/Product.php";
require_once "config/database.php";

$productModel = new Product($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $productId = $data['id'];
    $quantity = $data['quantity'];

    if (isset($_GET['action']) && $_GET['action'] === 'add') {
        // Thêm sản phẩm vào giỏ hàng
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += $quantity;
        } else {
            $product = $productModel->getProductById($productId);
            $_SESSION['cart'][$productId] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'description' => $product['description'],
                'price' => $product['price'],
                'quantity' => $quantity
            ];
        }
    } elseif (isset($_GET['action']) && $_GET['action'] === 'update') {
        // Cập nhật số lượng sản phẩm trong giỏ hàng
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] = $quantity;
        }
    }

    // Trả về dữ liệu giỏ hàng dưới dạng JSON
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'cart' => array_values($_SESSION['cart'])]);
    exit;
}
