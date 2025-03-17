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
        // Xóa các bản ghi liên quan trong bảng order_detail
        $stmt = $this->conn->prepare("DELETE FROM order_detail WHERE product_id = ?");
        $stmt->execute([$id]);

        // Xóa các bản ghi liên quan trong bảng comments
        $stmt = $this->conn->prepare("DELETE FROM comments WHERE product_id = ?");
        $stmt->execute([$id]);

        // Xóa sản phẩm
        $stmt = $this->conn->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }
    public function getProductsByCategory($category_id)
    {
        $stmt = $this->conn->prepare("SELECT id, name, price, description, image FROM products WHERE category_id = ?");
        $stmt->execute([$category_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductRating($product_id)
    {
        $stmt = $this->conn->prepare("SELECT AVG(rating) as average_rating, COUNT(rating) as total_reviews FROM comments WHERE product_id = ?");
        $stmt->execute([$product_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result['average_rating'] === null) {
            $result['average_rating'] = 0;
        }
        return $result;
    }

    public function getBestSellingProducts()
    {
        $stmt = $this->conn->prepare("SELECT * FROM products ORDER BY sales DESC LIMIT 10");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTopRatedProducts()
    {
        $stmt = $this->conn->prepare("
            SELECT p.*, COALESCE(AVG(r.rating), 0) AS average_rating
            FROM products p
            LEFT JOIN comments r ON p.id = r.product_id
            GROUP BY p.id
            ORDER BY average_rating DESC
            LIMIT 10
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
