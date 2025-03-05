<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

echo "Xin chào, " . $_SESSION["user"]["username"] . "! <a href='logout.php'>Đăng xuất</a>";
