<?php
session_start();

// Hàm kiểm tra xem người dùng đã đăng nhập hay chưa
function checkLogin()
{
    if (!isset($_SESSION["user_id"])) {
        header("Location: login.php");
        exit();
    }
}


// Hàm kiểm tra xem người dùng có phải là admin hay không
function checkAdmin()
{
    checkLogin(); // Kiểm tra đăng nhập trước
    if ($_SESSION["role"] !== "admin") {
        header("Location: unauthorized.php");
        exit();
    }
}

// Hàm thiết lập các biến phiên
function setSession($user)
{
    $_SESSION["user_id"] = $user["id"];
    $_SESSION["username"] = $user["username"];
    $_SESSION["role"] = $user["role"];
    $_SESSION["user"] = $user; // Thêm dòng này để lưu toàn bộ thông tin người dùng
}

// Hàm hủy phiên và đăng xuất
function logout()
{
    $_SESSION = array();
    session_destroy();
    header("Location:login.php");
    exit();
}

// Hàm thêm sản phẩm vào giỏ hàng
function addToCart($product_id, $quantity = 1)
{
    if (!isset($_SESSION["cart"])) {
        $_SESSION["cart"] = [];
    }

    if (isset($_SESSION["cart"][$product_id])) {
        $_SESSION["cart"][$product_id] += $quantity;
    } else {
        $_SESSION["cart"][$product_id] = $quantity;
    }
}

// Hàm tính tổng tiền trong giỏ hàng
function getTotalCartAmount()
{
    $total = 0;
    if (isset($_SESSION["cart"])) {
        foreach ($_SESSION["cart"] as $item) {
            $total += $item["price"] * $item["quantity"];
        }
    }
    return $total;
}
