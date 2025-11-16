<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-gray-900">任務標題</th>
                <th class="px-6 py-3 text-left text-gray-900">分類</th>
                <th class="px-6 py-3 text-left text-gray-900">標籤</th>
                <th class="px-6 py-3 text-left text-gray-900">狀態</th>
                <th class="px-6 py-3 text-left text-gray-900">委託人</th>
                <th class="px-6 py-3 text-left text-gray-900">工具人</th>
                <th class="px-6 py-3 text-left text-gray-900">建立日期</th>
                <th class="px-6 py-3 text-left text-gray-900">操作</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach ($tasks as $task): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-gray-900"><?= htmlspecialchars($task['title']) ?></td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-lg bg-blue-100 text-blue-800 text-sm"><?= htmlspecialchars($task['category']) ?></span>
                    </td>
                    <td class="px-6 py-4">
                        <?php 
                        $tags = explode(',', $task['tags'] ?? '');
                        foreach ($tags as $tag): ?>
                            <span class="px-2 py-1 rounded-lg bg-gray-200 text-gray-700 text-xs mr-1"><?= htmlspecialchars($tag) ?></span>
                        <?php endforeach; ?>
                    </td>
                    <td class="px-6 py-4">
                        <?php
                        $statusColors = [
                            'in_progress' => 'bg-yellow-100 text-yellow-800',
                            'completed' => 'bg-green-100 text-green-800',
                            'disputed' => 'bg-red-100 text-red-800',
                        ];
                        $colorClass = $statusColors[$task['status']] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <span class="px-2 py-1 rounded-lg <?= $colorClass ?> text-sm"><?= htmlspecialchars($task['status']) ?></span>
                    </td>
                    <td class="px-6 py-4 text-gray-700"><?= htmlspecialchars($task['requester']) ?></td>
                    <td class="px-6 py-4 text-gray-700"><?= htmlspecialchars($task['runner']) ?></td>
                    <td class="px-6 py-4 text-gray-700"><?= htmlspecialchars($task['created_at']) ?></td>
                    <td class="px-6 py-4 flex gap-2">
                        <button class="text-blue-600 hover:text-blue-700">查看</button>
                        <button class="text-red-600 hover:text-red-700">刪除</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>