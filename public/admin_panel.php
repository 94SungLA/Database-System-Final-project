<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../src/Database.php';

$db = new Database();
$conn = $db->getConnection();
?>

<!DOCTYPE html>
<html lang="zh-tw">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel (PHP)</title>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="p-4">

<div class="container">
    <h1 class="mb-4">管理者後台</h1>

    <!-- 搜尋 -->
    <form class="d-flex mb-3">
        <input type="text" name="search" class="form-control" placeholder="搜尋名稱或 Email"
               value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-primary ms-2">搜尋</button>
    </form>

    <!-- Add User Button -->
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addUserModal">
        新增使用者
    </button>

    <!-- Users Table -->
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>ID</th>
            <th>姓名</th>
            <th>Email</th>
            <th>狀態</th>
            <th>角色</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>

        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= $row['status'] ?></td>
            <td><?= $row['role'] ?></td>
            <td>

                <!-- Edit Button -->
                <button class="btn btn-warning btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#editUserModal<?= $row['id'] ?>">
                    編輯
                </button>

                <!-- Delete -->
                <a href="?delete=<?= $row['id'] ?>"
                   class="btn btn-danger btn-sm"
                   onclick="return confirm('確定要刪除嗎？');">
                    刪除
                </a>
            </td>
        </tr>

        <!-- Edit Modal -->
        <div class="modal fade" id="editUserModal<?= $row['id'] ?>" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" class="modal-content">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">

                    <div class="modal-header">
                        <h5 class="modal-title">編輯使用者</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <label>姓名</label>
                        <input name="name" class="form-control mb-2" value="<?= htmlspecialchars($row['name']) ?>">

                        <label>Email</label>
                        <input name="email" class="form-control mb-2" value="<?= htmlspecialchars($row['email']) ?>">

                        <label>狀態</label>
                        <select name="status" class="form-control mb-2">
                            <option value="Active" <?= $row['status']=="Active"?"selected":"" ?>>Active</option>
                            <option value="Inactive" <?= $row['status']=="Inactive"?"selected":"" ?>>Inactive</option>
                        </select>

                        <label>角色</label>
                        <input name="role" class="form-control" value="<?= $row['role'] ?>">
                    </div>

                    <div class="modal-footer">
                        <button name="edit" class="btn btn-primary">儲存</button>
                    </div>
                </form>
            </div>
        </div>

        <?php endwhile; ?>

        </tbody>
    </table>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">新增使用者</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <label>姓名</label>
                <input name="name" class="form-control mb-2">

                <label>Email</label>
                <input name="email" class="form-control mb-2">

                <label>狀態</label>
                <select name="status" class="form-control mb-2">
                    <option>Active</option>
                    <option>Inactive</option>
                </select>

                <label>角色</label>
                <input name="role" class="form-control">
            </div>

            <div class="modal-footer">
                <button name="add" class="btn btn-success">新增</button>
            </div>

        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>