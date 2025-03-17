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
        echo "<div class='alert alert-success'>Cập nhật vai trò thành công! <a href='manage_users.php'>Quay lại</a></div>";
    } else {
        echo "<div class='alert alert-danger'>Lỗi khi cập nhật vai trò!</div>";
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
        <div class="pc-content">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <h2 class="mb-4">Sửa vai trò người dùng</h2>
                    <form method="post">
                        <div class="mb-3">
                            <label for="role" class="form-label">Vai trò</label>
                            <select name="role" id="role" class="form-select" required>
                                <option value="user" <?= $user["role"] == "user" ? "selected" : "" ?>>User</option>
                                <option value="admin" <?= $user["role"] == "admin" ? "selected" : "" ?>>Admin</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                        <a href="manage_users.php" class="btn btn-secondary">Quay lại</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

<?php include 'footer_admin.php'; ?>