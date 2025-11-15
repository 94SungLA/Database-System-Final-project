<?php
session_start();

$newUserName = $_SESSION["user"]["name"];
$newUserPhone = $_SESSION["user"]["phone"];
$newEmail = $_SESSION["user"]["email"];

function updateAndGoBack($newUserName, $newPhone, $newEmail) {
    // call update function
    echo ($newEmail);
    if ($newUserName == "" | $newPhone == "" | $newEmail == "") {
        echo "<p class='text-red-600 mb-2'>請填寫所有必填欄位。</p>";
        return;
    }
    else {
        echo "<script>window.history.back();</script>";
    }
    exit;
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
            onclick="window.history.back()"
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
          onclick="window.history.back()"
          class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
        >
          取消
        </button>
        <button
          type="button"
          onclick="updateAndGoBack($newUserName, $newUserPhone, $newEmail)"
          class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
        >
          儲存
        </button>
      </div>

    </form>
  </div>
</div>

</body>
</html>
