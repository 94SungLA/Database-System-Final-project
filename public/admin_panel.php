<?php
// ====== Data loading interface ======
// 你日後自己實作 SQL
function getStats() { return []; }
function getUsers() { return []; }
function getTasks() { return []; }

// 取得資料
$stats = getStats();
$users = getUsers();
$tasks = getTasks();
?>

<?php
    $adminActiveTab = null;
    $stats = [
        [
            'label' => '總用戶數',
            'value' => count($users),
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" /></svg>',
            'color' => 'bg-blue-500',
        ],
        [
            'label' => '進行中任務',
            'value' => count(array_filter($tasks, function($t) { return $t['status'] === 'in_progress'; })),
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>',
            'color' => 'bg-green-500',
        ],
        [
            'label' => '今日完成',
            'value' => count(array_filter($tasks, function($t) { return $t['status'] === 'completed'; })),
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0 1 18 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3 1.5 1.5 3-3.75" /></svg>',
            'color' => 'bg-purple-500',
        ],
        [
            'label' => '待處理爭議',
            'value' => count(array_filter($tasks, function($t) { return $t['status'] === 'disputed'; })),
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>',
            'color' => 'bg-red-500',
        ],
    ];
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8" />
    <title>管理員面板</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/heroicons@2.0.13"></script>
</head>

<body class="bg-gray-100">

<div class="mb-6">
    <h2 class="text-gray-900 mb-2">管理員面板</h2>
    <p class="text-gray-600">監控平台活動與用戶狀態</p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

    <?php foreach ($stats as $index => $stat): ?>
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center gap-4">
                <div class="<?= $stat['color'] ?> w-12 h-12 rounded-lg flex items-center justify-center">
                    <!-- icon -->
                    <?= $stat['icon'] ?>
                </div>

                <div>
                    <p class="text-gray-600"><?= $stat['label'] ?></p>
                    <p class="text-gray-900"><?= $stat['value'] ?></p>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

</div>

<!-- Tabs -->
<?php $adminActiveTab = $_GET['adminTab'] ?? 'overview';  ?>

<!-- Tabs -->
<div class="flex gap-2 mb-8">

    <!-- 總覽 -->
    <a href="?adminTab=overview">
        <button
            class="px-4 py-2 rounded-lg transition-colors 
            <?php echo $adminActiveTab === 'overview'
                ? 'bg-blue-600 text-white'
                : 'bg-white text-gray-700 border border-gray-300 hover:border-blue-600'; ?>">
            總覽
        </button>
    </a>

    <!-- 用戶管理 -->
    <a href="?adminTab=users">
        <button
            class="px-4 py-2 rounded-lg transition-colors
            <?php echo $adminActiveTab === 'users'
                ? 'bg-blue-600 text-white'
                : 'bg-white text-gray-700 border border-gray-300 hover:border-blue-600'; ?>">
            用戶管理
        </button>
    </a>

    <!-- 任務管理 -->
    <a href="?adminTab=tasks">
        <button
            class="px-4 py-2 rounded-lg transition-colors
            <?php echo $adminActiveTab === 'tasks'
                ? 'bg-blue-600 text-white'
                : 'bg-white text-gray-700 border border-gray-300 hover:border-blue-600'; ?>">
            任務管理
        </button>
    </a>

</div>
<!-- Overview -->
<?php
    if ($adminActiveTab === 'overview') {
        include './component/overview.php';
    }
    elseif ($adminActiveTab === 'users') {
        include './component/users.php';
    }
    elseif ($adminActiveTab === 'tasks') {
        include './component/tasks.php';
    }
?>
