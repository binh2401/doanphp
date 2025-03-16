<?php
require_once "../config/database.php";
require_once "../public/session.php"; // Quản lý phiên
checkAdmin(); // Kiểm tra xem user có phải admin không

if (!isset($_GET["id"])) {
    header("Location: manage_users.php");
    exit();
}

$user_id = $_GET["id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST["role"];
    $stmt = $conn->prepare("UPDATE users SET role = :role WHERE id = :id");
    $stmt->bindParam(":role", $role);
    $stmt->bindParam(":id", $user_id);
    if ($stmt->execute()) {
        echo "Cập nhật vai trò thành công! <a href='manage_users.php'>Quay lại</a>";
    } else {
        echo "Lỗi khi cập nhật vai trò!";
    }
} else {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(":id", $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        header("Location: manage_users.php");
        exit();
    }
}
?>

<?php include 'header_admin.php'; ?>

<body>
    <div class="pc-container">
        <h2>Sửa vai trò người dùng</h2>
        <form method="post">
            <label for="role">Vai trò:</label>
            <select name="role" id="role" required>
                <option value="user" <?= $user["role"] == "user" ? "selected" : "" ?>>User</option>
                <option value="admin" <?= $user["role"] == "admin" ? "selected" : "" ?>>Admin</option>
            </select><br>
            <button type="submit">Cập nhật</button>
        </form>
        <a href="manage_users.php">Quay lại</a>
    </div>
</body>

<?php include 'footer_admin.php'; ?>