<?php
session_start();
require_once "../backend/auth.php";
require_once "../backend/user.php";
requireLogin();
// 假設這些變數由你的後端提供

?>
<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="utf-8">
    <title>跑腿任務平台</title>

    <!-- Tailwind CDN -->
    <meta name="tailwindcss" content="no-warning">
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Heroicons CDN（可用 React 的 <Icon /> 替代品） -->
    <script src="https://unpkg.com/heroicons@2.0.13"></script>
</head>

<body class="min-h-screen bg-gray-50">

    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">

                <!-- 左邊：平台名稱 -->
                <div>
                    <h1 class="text-gray-900">跑腿任務平台</h1>
                    <p class="text-gray-600">
                        <?= isAdmin() ? "管理員控制台" : "輕鬆發布與接取任務" ?>
                    </p>
                </div>

                <!-- 右上角：使用者資訊 + 登出 -->
                <div class="flex items-center gap-3">

                    <!-- 使用者資訊 -->
                    <a href="userInfo.php"
                        class="text-right hover:bg-gray-50 px-4 py-2 rounded-lg transition-colors block">
                        <div class="flex items-center gap-2">

                            <?php if (isAdmin()): ?>
                                <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded-full text-sm">
                                    管理員
                                </span>
                            <?php endif; ?>

                            <div>
                                <p class="text-gray-900"><?= $_SESSION["user"]["name"] ?></p>
                                <p class="text-gray-600">
                                    <?= $_SESSION["user"]["phone"] ?> · <?= $_SESSION["user"]["email"] ?>
                                </p>
                            </div>
                        </div>
                    </a>

                    <!-- 登出按鈕 -->
                    <a href="logout.php"
                        class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors"
                        title="登出">
                        <!-- Heroicon: arrow-left-on-rectangle -->
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25V9m9 6v3.75A2.25 2.25 0 0113.5 21h-6A2.25 2.25 0 015.25 18.75V15m12-6l3 3m0 0l-3 3m3-3H9">
                            </path>
                        </svg>
                        <span>登出</span>
                    </a>

                </div>

            </div>

            <!-- Tabs -->
            <div class="flex gap-1 -mb-px">
                <?php if (!empty(isAdmin()) && isAdmin() === true): ?>
                    <?php
                    // 管理員專屬 tab
                    $adminTab = ["id" => "admin", "label" => "管理面板"];
                    $activeTab = $_GET['tab'] ?? 'admin';
                    $isActive = ($activeTab === $adminTab['id']);
                    ?>
                    <a href="?tab=<?= $adminTab['id'] ?>" class="flex items-center gap-2 px-6 py-3 border-b-2 transition-colors
                    <?= $isActive
                        ? 'border-blue-600 text-blue-600'
                        : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'
                    ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <?= $adminTab['label'] ?>
                    </a>

                <?php else: ?>
                    <?php
                    // 普通使用者 tabs
                    $userTabs = [
                        ["id" => "taskboard", "label" => "任務看板", "icon" => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" /></svg>'],
                        ["id" => "mytasks", "label" => "我的任務", "icon" => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg> ']
                    ];
                    foreach ($userTabs as $tab):
                        $activeTab = $_GET['tab'] ?? 'taskboard';
                        $isMyTasksSection = in_array($activeTab, ['mytasks', 'published', 'accepted']);
                        $isActive = ($tab['id'] === 'mytasks' && $isMyTasksSection) || $activeTab === $tab['id'];
                    ?>
                        <a href="?tab=<?= $tab['id'] ?>" class="flex items-center gap-2 px-6 py-3 border-b-2 transition-colors
                        <?= $isActive
                            ? 'border-blue-600 text-blue-600'
                            : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'
                        ?>">
                            <!-- Icon -->
                            <?= $tab['icon'] ?>
                            <?= $tab['label'] ?>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <?php if (!isAdmin()): ?>

            <?php if ($activeTab === 'taskboard'): ?>
                <div class="mb-4">
                    <a href="create_task.php" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        發布新任務
                    </a>
                </div>
            <?php endif; ?>

            <?php
            $activeTab = $_GET['tab'] ?? 'taskboard';
            if ($activeTab === 'taskboard') {
                include "components/viewTask.php";
            } elseif (in_array($activeTab, ['mytasks', 'published', 'accepted'])) {
                include "components/myTask.php";
            }
            ?>

        <?php endif; ?>

        <?php if (isAdmin()): ?>
            <?php if ($activeTab === 'admin')
                include "admin_panel.php"; ?>

        <?php endif; ?>

    </main>

</body>

</html>