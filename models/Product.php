<?php
require_once __DIR__ . "/../config/database.php";

class Product
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Thêm sản phẩm
    public function addProduct($name, $price, $description, $image, $category_id)
    {
        $stmt = $this->conn->prepare("INSERT INTO products (name, price, description, image, category_id) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$name, $price, $description, $image, $category_id]);
    }


    // Lấy danh sách sản phẩm

    public function getProducts()
    {
        $stmt = $this->conn->prepare("SELECT id, name, price, description, image, sold_quantity FROM products");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy thông tin sản phẩm theo ID
    public function getProductById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Cập nhật sản phẩm
    public function updateProduct($id, $name, $price, $description, $image)
    {
        $stmt = $this->conn->prepare("UPDATE products SET name = ?, price = ?, description = ?, image = ? WHERE id = ?");
        return $stmt->execute([$name, $price, $description, $image, $id]);
    }

    // Xóa sản phẩm
    public function deleteProduct($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
