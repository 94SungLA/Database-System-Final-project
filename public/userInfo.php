<?php
require_once "../backend/user.php";
session_start();

$newUserName = $_SESSION["user"]["name"];
$newUserPhone = $_SESSION["user"]["phone"];
$newEmail = $_SESSION["user"]["email"];
$msg = "";
$status = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION["user"]["user_id"];
    $name = $_POST["name"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];


    $result = updateUserProfile($user_id, $name, $phone, $email);

    if ($result === "email_taken") {
        $status = false;
        $msg = "Email 已被其他使用者使用。";
    } elseif ($result === "success") {
        $status = true;
        $msg = "更新成功";
    }
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
<meta charset="UTF-8">
<title>編輯個人資料</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<!-- Modal 背景 -->
<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">

  <!-- Modal 主體 -->
  <div class="bg-white rounded-lg max-w-md w-full shadow-lg">
    
    <!-- 標題列 -->
    <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
      <h2 class="text-gray-900 text-lg font-semibold">編輯個人資料</h2>
      <button
            onclick="window.location.href = 'index.php'"
            class="text-3xl text-gray-400 hover:text-4xl hover:font-bold hover:text-red-600 transition-all duration-200"
        >
            &times;
        </button>
    </div>

    <!-- 表單 -->
    <form method="POST" class="p-6 space-y-4">

      <div>
        <label class="block text-gray-900 mb-2">姓名 *</label>
        <input
          type="text"
          name="name"
          required
          value="<?= htmlspecialchars($newUserName) ?>"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
        >
      </div>

      <div>
        <label class="block text-gray-900 mb-2">電話 *</label>
        <input
          type="tel"
          name="phone"
          required
          value="<?= htmlspecialchars($newUserPhone) ?>"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
        >
      </div>

      <div>
        <label class="block text-gray-900 mb-2">Email *</label>
        <input
          type="email"
          name="email"
          required
          value="<?= htmlspecialchars($newEmail) ?>"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
        >
      </div>

      <div class="flex gap-3 pt-4">
        <button
          type="button"
          onclick="window.location.href = 'index.php'"
          class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
        >
          取消
        </button>
        <button type="submit"
            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            儲存
        </button>
      </div>
        <?php if ($status !== null): ?>
            <?php if ($status === false): ?>
                <div class="flex items-center justify-center h-10 bg-red-300 rounded-lg mt-4">
                    <p><?= $msg ?></p>
                </div>
            <?php endif; ?>
            <?php if ($status === true): ?>
                <div class="flex items-center justify-center h-10 bg-green-300 rounded-lg mt-4">
                    <p><?= $msg ?></p>
                    <script>
                        setTimeout(function() {
                            window.location.href = "index.php";
                        }, 500);
                    </script>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </form>
  </div>
</div>

</body>
</html>
