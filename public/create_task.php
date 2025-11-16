<?php
session_start();
require_once "../backend/auth.php";
require_once "../backend/task.php";
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);
    $tags = trim($_POST['tags']);
    $reward = (float)$_POST['reward'];
    $deadline = $_POST['deadline'];
    $requester_id = $_SESSION['user']['user_id'];

    if (!empty($title) && !empty($desc) && !empty($tags) && $reward > 0 && !empty($deadline)) {
        if (createTask($title, $desc, $tags, $reward, $deadline, $requester_id)) {
            header("Location: index.php?tab=taskboard");
            exit;
        } else {
            $error = "創建任務失敗，請重試。";
        }
    } else {
        $error = "請填寫所有必填字段。";
    }
}
?>
<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="utf-8">
    <title>發布新任務 - 跑腿任務平台</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-gray-50">
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-gray-900">跑腿任務平台</h1>
                    <p class="text-gray-600">輕鬆發布與接取任務</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="userInfo.php" class="text-right hover:bg-gray-50 px-4 py-2 rounded-lg transition-colors block">
                        <div class="flex items-center gap-2">
                            <div>
                                <p class="text-gray-900"><?= $_SESSION["user"]["name"] ?></p>
                                <p class="text-gray-600"><?= $_SESSION["user"]["phone"] ?> · <?= $_SESSION["user"]["email"] ?></p>
                            </div>
                        </div>
                    </a>
                    <a href="logout.php" class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors" title="登出">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25V9m9 6v3.75A2.25 2.25 0 0113.5 21h-6A2.25 2.25 0 015.25 18.75V15m12-6l3 3m0 0l-3 3m3-3H9"></path>
                        </svg>
                        <span>登出</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="max-w-2xl mx-auto bg-white p-6 shadow rounded-xl">
            <h2 class="text-2xl font-bold mb-4">發布新任務</h2>
            <?php if (isset($error)): ?>
                <p class="text-red-500 mb-4"><?= $error ?></p>
            <?php endif; ?>
            <form method="POST" class="space-y-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">任務標題</label>
                    <input type="text" id="title" name="title" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">任務描述</label>
                    <textarea id="description" name="description" rows="4" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                <div>
                    <label for="tags" class="block text-sm font-medium text-gray-700">標籤（用逗號分隔）</label>
                    <input type="text" id="tags" name="tags" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="reward" class="block text-sm font-medium text-gray-700">獎勵（元）</label>
                    <input type="number" id="reward" name="reward" step="0.01" min="0" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="deadline" class="block text-sm font-medium text-gray-700">截止日期</label>
                    <input type="datetime-local" id="deadline" name="deadline" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="flex gap-4">
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">發布任務</button>
                    <a href="index.php?tab=taskboard" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors">取消</a>
                </div>
            </form>
        </div>
    </main>
</body>

</html>