<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once "../backend/auth.php";
require_once "../backend/task.php";
requireLogin();

$user_id = $_SESSION['user']['user_id'];
global $pdo;
$stmt = $pdo->prepare("SELECT user_id FROM Users WHERE user_id = ?");
$stmt->execute([$user_id]);
if (!$stmt->fetch()) {
    die("用戶不存在，請重新登錄。");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);
    $tags = $_POST['tags'] ?? '';
    $location_tags = trim($_POST['location_tags']);
    $reward = (float)$_POST['reward'];
    $deadline = str_replace('T', ' ', $_POST['deadline']) . ':00';
    $requester_id = $user_id;

    if (!empty($title) && !empty($desc) && !empty($tags) && $reward > 0 && !empty($deadline)) {
        if (createTask($title, $desc, $tags, $reward, $deadline, $location_tags, $requester_id)) {
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
    <script src="https://unpkg.com/heroicons@2.0.13"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>

<body class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
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

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="max-w-3xl mx-auto bg-white p-8 shadow-2xl rounded-2xl border border-gray-100">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">發布新任務</h2>
                <p class="text-gray-600">填寫以下資訊，讓工具人輕鬆接取您的任務</p>
            </div>
            <?php if (isset($error)): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-red-700 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"></path>
                        </svg>
                        <?= $error ?>
                    </p>
                </div>
            <?php endif; ?>
            <form method="POST" class="space-y-6">
                <div>
                    <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                        <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3h6m-6 3h9"></path>
                        </svg>
                        任務標題
                    </label>
                    <input type="text" id="title" name="title" placeholder="輸入任務標題，例如：幫我買咖啡" required class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                </div>
                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                        <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"></path>
                        </svg>
                        任務描述
                    </label>
                    <textarea id="description" name="description" rows="4" placeholder="詳細描述任務內容，例如：去星巴克買一杯拿鐵，送到宿舍" required class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"></path>
                        </svg>
                        標籤（單選）
                    </label>
                    <div class="mt-2 grid grid-cols-2 gap-4">
                        <label class="inline-flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition duration-200">
                            <input type="radio" name="tags" value="跑腿" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
                            <span class="ml-2 font-medium">跑腿</span>
                        </label>
                        <label class="inline-flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition duration-200">
                            <input type="radio" name="tags" value="代購" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
                            <span class="ml-2 font-medium">代購</span>
                        </label>
                        <label class="inline-flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition duration-200">
                            <input type="radio" name="tags" value="送件" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
                            <span class="ml-2 font-medium">送件</span>
                        </label>
                        <label class="inline-flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition duration-200">
                            <input type="radio" name="tags" value="其他" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
                            <span class="ml-2 font-medium">其他</span>
                        </label>
                    </div>
                </div>
                <div>
                    <label for="location_tags" class="block text-sm font-semibold text-gray-700 mb-2">
                        <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.458-7.5 11.458S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"></path>
                        </svg>
                        位置標籤
                    </label>
                    <input type="text" id="location_tags" name="location_tags" placeholder="輸入位置，例如：台大校園" class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                </div>
                <div>
                    <label for="reward" class="block text-sm font-semibold text-gray-700 mb-2">
                        <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        報酬（元）
                    </label>
                    <input type="number" id="reward" name="reward" step="0.01" min="0" placeholder="輸入金額，例如：50" required class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                </div>
                <div>
                    <div class="block text-sm font-semibold text-gray-700 mb-2"> <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5a2.25 2.25 0 002.25-2.25m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5a2.25 2.25 0 012.25 2.25v7.5"></path>
                        </svg> 截止日期 </div> <input
                        type="text"
                        id="deadline"
                        name="deadline"
                        class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg"
                        placeholder="選擇日期與時間" />
                </div>
                <script>
                    flatpickr("#deadline", {
                        enableTime: true,
                        dateFormat: "Y-m-d H:i",
                        time_24hr: true,
                    });
                </script>
                <div class="flex gap-4 pt-4">
                    <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 font-semibold shadow-lg">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path>
                        </svg>
                        發布任務
                    </button>
                    <a href="index.php?tab=taskboard" class="px-6 py-3 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition duration-200 font-semibold shadow-lg">取消</a>
                </div>
            </form>
        </div>
    </main>
</body>

</html>