<?php
require_once __DIR__ . "/../../backend/task.php";
$tasks = getOpenTasks();

// Helper function to format deadline
function formatDeadline($deadline)
{
    if (!$deadline) {
        return '無';
    }
    $date = new DateTime($deadline);
    return $date->format('Y-m-d H:i');
}
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if (empty($tasks)): ?>
        <div class="col-span-full text-center py-12 bg-white rounded-lg border border-gray-200">
            <p class="text-gray-600">目前沒有可用的任務</p>
        </div>
    <?php else: ?>
        <?php foreach ($tasks as $task): ?>
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="mb-4">
                    <h3 class="text-gray-900 font-bold text-lg mb-2"><?= htmlspecialchars($task["title"]) ?></h3>
                    <div class="flex flex-wrap gap-2">
                        <?php if (!empty($task['tags'])): ?>
                            <span class="px-3 py-1 rounded-full bg-purple-100 text-purple-700 text-sm">
                                <?= htmlspecialchars($task['tags']) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <p class="text-gray-600 mb-4 line-clamp-2 h-12"><?= htmlspecialchars($task["description"]) ?></p>

                <div class="space-y-2 mb-4">
                    <div class="flex items-center gap-2 text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="text-green-600">
                            <line x1="12" y1="1" x2="12" y2="23"></line>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                        <span>酬勞：NT$ <?= htmlspecialchars($task["reward"]) ?></span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="text-red-600">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        <span>地點：(待新增)</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="text-blue-600">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <span>截止：<?= formatDeadline($task["deadline"]) ?></span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="text-purple-600">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span><?= htmlspecialchars($task["requester_name"]) ?> ⭐
                            <?= htmlspecialchars($task["rating_as_requester"] ?? 'N/A') ?></span>
                    </div>
                </div>

                <div class="flex gap-2">
                    <a href="/public/viewTask.php?id=<?= $task['task_id'] ?>"
                        class="flex-1 flex items-center justify-center gap-2 px-4 py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        查看詳情
                    </a>
                    <?php if (isset($_SESSION['user'])): ?>
                        <form action="/backend/task_handler.php" method="POST" class="flex-1">
                            <input type="hidden" name="task_id" value="<?= $task['task_id'] ?>">
                            <input type="hidden" name="action" value="apply">
                            <button type="submit"
                                class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                申請接單
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>