<?php
require_once "../backend/admin";
// 假設 $tasks 來自資料庫查詢，例如：
$tasks = adminGetAllTasks();
var_dump($tasks);

$statusColors = [
    'pending' => 'bg-yellow-500',
    'in_progress' => 'bg-blue-500',
    'completed' => 'bg-green-500',
    'cancelled' => 'bg-gray-500',
    'disputed' => 'bg-red-500'
];

function getStatusLabel($status) {
    return match($status) {
        'pending' => '待接任務',
        'in_progress' => '進行中',
        'completed' => '已完成',
        'cancelled' => '已取消',
        'disputed' => '爭議中',
        default => '未知',
    };
}
?>

<div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-gray-900">任務標題</th>
                    <th class="px-6 py-3 text-left text-gray-900">類別</th>
                    <th class="px-6 py-3 text-left text-gray-900">委託人</th>
                    <th class="px-6 py-3 text-left text-gray-900">工具人</th>
                    <th class="px-6 py-3 text-left text-gray-900">狀態</th>
                    <th class="px-6 py-3 text-left text-gray-900">酬勞</th>
                    <th class="px-6 py-3 text-left text-gray-900">檢舉數</th>
                    <th class="px-6 py-3 text-left text-gray-900">操作</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach($tasks as $task): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-gray-900"><?= htmlspecialchars($task['title']) ?></td>
                    <td class="px-6 py-4 text-gray-700"><?= htmlspecialchars($task['category']) ?></td>
                    <td class="px-6 py-4 text-gray-700"><?= htmlspecialchars($task['requester']) ?></td>
                    <td class="px-6 py-4 text-gray-700">
                        <span class="<?= $task['runner'] ? '' : 'italic text-gray-400' ?>">
                            <?= $task['runner'] ?: '-' ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-white <?= $statusColors[$task['status']] ?? 'bg-gray-300' ?>">
                            <?= getStatusLabel($task['status']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-700">NT$ <?= number_format($task['reward']) ?></td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-0.5 rounded-full text-sm <?= $task['reportCount'] > 0 ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-700' ?>">
                            <?= $task['reportCount'] ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <a href="view_task.php?id=<?= $task['id'] ?>" class="text-blue-600 hover:text-blue-700">查看</a>
                            <?php if($task['status'] === 'disputed'): ?>
                                <a href="handle_task.php?id=<?= $task['id'] ?>" class="text-red-600 hover:text-red-700">處理</a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
