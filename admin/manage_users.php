<?php
require_once "../config/database.php";
require_once "../public/session.php"; // Quản lý phiên
checkAdmin(); // Kiểm tra xem user có phải admin không

$stmt = $conn->prepare("SELECT * FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý người dùng</title>
</head>

<body>
    <h2>Quản lý người dùng</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Tên người dùng</th>
            <th>Email</th>
            <th>Vai trò</th>
            <th>Hành động</th>
        </tr>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user["id"] ?></td>
                <td><?= $user["username"] ?></td>
                <td><?= $user["email"] ?></td>
                <td><?= $user["role"] ?></td>
                <td>
                    <a href="edit_role.php?id=<?= $user["id"] ?>">Sửa vai trò</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>