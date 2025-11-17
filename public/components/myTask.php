<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect if not logged in
if (!isset($_SESSION["user"])) {
    header("Location: /public/login.php");
    exit();
}

require_once __DIR__ . "/../../backend/task.php";

$user_id = $_SESSION["user"]["user_id"];
$asRequester = getTasksByRequesterID($user_id);
$asRunner = getTasksByRunnerID($user_id);

// Determine active tab and status filter from GET params
$activeTab = $_GET['tab'] ?? 'mytasks';
if ($activeTab !== 'published' && $activeTab !== 'accepted') {
    $activeTab = 'published'; // Default to published within mytasks
}
$statusFilter = $_GET['status'] ?? 'all';

// Helper functions
function getStatusInfo($status, $role = 'requester')
{
    $map = [
        'open' => ['label' => '待接單', 'color' => 'bg-yellow-500'],
        'confirming' => ['label' => '待確認', 'color' => 'bg-orange-500'],
        'in_progress' => ['label' => '進行中', 'color' => 'bg-blue-500'],
        'completed' => ['label' => '已完成', 'color' => 'bg-green-500'],
        'cancelled' => ['label' => '已取消', 'color' => 'bg-gray-500'],
    ];
    return $map[$status] ?? ['label' => '未知', 'color' => 'bg-gray-300'];
}

function formatDeadline($deadline)
{
    return $deadline ? (new DateTime($deadline))->format('Y-m-d H:i') : '無';
}

function getIcon($name)
{
    $icons = [
        'dollar' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-600"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>',
        'clock' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-600"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>',
        'user' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-purple-600"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>',
        'eye' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>',
        'check' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>',
        'cancel' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>',
        'star' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>',
    ];
    return $icons[$name] ?? '';
}

// Calculate status counts for filters
$publishedStatusCounts = array_count_values(array_column($asRequester, 'status'));
$acceptedStatusCounts = array_count_values(array_column($asRunner, 'status'));

$publishedFilters = [
    'all' => ['label' => '全部', 'count' => count($asRequester)],
    'open' => ['label' => '待接單', 'count' => $publishedStatusCounts['open'] ?? 0],
    'confirming' => ['label' => '待確認', 'count' => $publishedStatusCounts['confirming'] ?? 0],
    'in_progress' => ['label' => '進行中', 'count' => $publishedStatusCounts['in_progress'] ?? 0],
    'completed' => ['label' => '已完成', 'count' => $publishedStatusCounts['completed'] ?? 0],
    'cancelled' => ['label' => '已取消', 'count' => $publishedStatusCounts['cancelled'] ?? 0],
];

$acceptedFilters = [
    'all' => ['label' => '全部', 'count' => count($asRunner)],
    'confirming' => ['label' => '待確認', 'count' => $acceptedStatusCounts['confirming'] ?? 0],
    'in_progress' => ['label' => '進行中', 'count' => $acceptedStatusCounts['in_progress'] ?? 0],
    'completed' => ['label' => '已完成', 'count' => $acceptedStatusCounts['completed'] ?? 0],
];

$currentTasks = ($activeTab === 'published') ? $asRequester : $asRunner;
$currentFilters = ($activeTab === 'published') ? $publishedFilters : $acceptedFilters;

// Apply status filter
$filteredTasks = ($statusFilter === 'all')
    ? $currentTasks
    : array_filter($currentTasks, fn($task) => $task['status'] === $statusFilter);

?>

<div>
    <?php if (isset($_SESSION['message'])): ?>
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            <?= htmlspecialchars($_SESSION['message']) ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">我的任務</h2>
        <p class="text-gray-600">管理您發布和接取的任務</p>
    </div>

    <!-- Main Tabs -->
    <div class="flex gap-2 mb-4">
        <a href="?tab=published"
            class="flex-1 text-center px-4 py-3 rounded-lg transition-colors <?= $activeTab === 'published' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:border-blue-600' ?>">
            我發布的 (<?= count($asRequester) ?>)
        </a>
        <a href="?tab=accepted"
            class="flex-1 text-center px-4 py-3 rounded-lg transition-colors <?= $activeTab === 'accepted' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:border-blue-600' ?>">
            我接取的 (<?= count($asRunner) ?>)
        </a>
    </div>

    <!-- Status Filter -->
    <div class="flex gap-2 mb-6 overflow-x-auto pb-2">
        <?php foreach ($currentFilters as $key => $filter): ?>
            <a href="?tab=<?= $activeTab ?>&status=<?= $key ?>"
                class="px-4 py-2 rounded-lg whitespace-nowrap transition-colors <?= $statusFilter === $key ? 'bg-purple-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:border-purple-600' ?>">
                <?= $filter['label'] ?> (<?= $filter['count'] ?>)
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Tasks List -->
    <div class="space-y-4">
        <?php if (empty($filteredTasks)): ?>
            <div class="text-center py-12 bg-white rounded-lg border border-gray-200">
                <p class="text-gray-600">此分類下沒有任務</p>
            </div>
        <?php else: ?>
            <?php foreach ($filteredTasks as $task):
                $statusInfo = getStatusInfo($task['status']);
            ?>
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-bold text-gray-900"><?= htmlspecialchars($task["title"]) ?></h3>
                                <span
                                    class="px-3 py-1 text-sm rounded-full text-white <?= $statusInfo['color'] ?>"><?= $statusInfo['label'] ?></span>
                            </div>
                            <p class="text-gray-600 line-clamp-2"><?= htmlspecialchars($task["description"]) ?></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 border-t pt-4">
                        <div class="flex items-center gap-2 text-gray-700">
                            <?= getIcon('dollar') ?>
                            <span>酬勞：NT$ <?= htmlspecialchars($task["reward"]) ?></span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-700">
                            <?= getIcon('clock') ?>
                            <span>截止：<?= formatDeadline($task["deadline"]) ?></span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-700">
                            <?= getIcon('user') ?>
                            <?php if ($activeTab === 'published'): ?>
                                <span>執行者：<?= htmlspecialchars($task['runner_name'] ?? '尚未指派') ?></span>
                            <?php else: ?>
                                <span>發布者：<?= htmlspecialchars($task['requester_name'] ?? 'N/A') ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($task['status'] === 'confirming' && $activeTab === 'published'): ?>
                        <div class="mb-4 p-3 bg-orange-50 rounded-lg border border-orange-200">
                            <p class="text-gray-700">
                                <span class="font-semibold"><?= htmlspecialchars($task['runner_name']) ?></span> 申請了此任務，等待您的確認。
                            </p>
                        </div>
                    <?php endif; ?>

                    <?php if ($task['status'] === 'completed' && isset($task['rating']) && !empty($task['rating'])): ?>
                        <div class="mb-4 p-3 bg-yellow-50 rounded-lg">
                            <div class="flex items-center gap-2 mb-1">
                                <?= getIcon('star') ?>
                                <span class="text-gray-900 font-semibold">您給出的評價：<?= htmlspecialchars($task['rating']) ?> / 5</span>
                            </div>
                            <p class="text-gray-700"><?= htmlspecialchars($task['comment']) ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    <div class="flex gap-3 flex-wrap">
                        <a href="index.php?tab=taskboard&task_id=<?= $task['task_id'] ?>"
                            class="flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            <?= getIcon('eye') ?>
                            查看詳情
                        </a>

                        <form action="/Database-System-Final-project/backend/task_handler.php" method="POST" class="contents">
                            <input type="hidden" name="task_id" value="<?= $task['task_id'] ?>">
                            <input type="hidden" name="runner_id" value="<?= htmlspecialchars($task['runner_id'] ?? '') ?>">
                            <input type="hidden" name="source" value="myTask">

                            <?php if ($activeTab === 'published'): ?>
                                <?php if ($task['status'] === 'confirming'): ?>
                                    <button type="submit" name="action" value="approve"
                                        class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                        <?= getIcon('check') ?> 批准申請
                                    </button>
                                    <button type="submit" name="action" value="reject"
                                        class="flex items-center gap-2 px-4 py-2 border border-red-600 text-red-600 rounded-lg hover:bg-red-50 transition-colors">
                                        <?= getIcon('cancel') ?> 拒絕申請
                                    </button>
                                <?php elseif ($task['status'] === 'in_progress'): ?>
                                    <button type="submit" name="action" value="complete_by_requester"
                                        class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                        <?= getIcon('check') ?> 確認完成
                                    </button>
                                <?php elseif (in_array($task['status'], ['open', 'confirming'])): ?>
                                    <button type="submit" name="action" value="cancel"
                                        class="flex items-center gap-2 px-4 py-2 border border-red-600 text-red-600 rounded-lg hover:bg-red-50 transition-colors">
                                        <?= getIcon('cancel') ?> 取消任務
                                    </button>
                                <?php elseif ($task['status'] === 'completed'): ?>
                                    <?php if (empty($task['rating'])): ?>
                                        <button class="review-btn flex items-center gap-2 px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors"
                                            data-task-id="<?= $task['task_id'] ?>"
                                            data-reviewee-id="<?= $activeTab === 'published' ? $task['runner_id'] : $task['requester_id'] ?>"
                                            data-role="<?= $activeTab === 'published' ? 'runner' : 'requester' ?>">
                                            <?= getIcon('star') ?> 評價<?= $activeTab === 'published' ? '執行者' : '發布者' ?>
                                        </button>
                                    <?php else: ?>
                                        <button class="review-btn flex items-center gap-2 px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors"
                                            data-task-id="<?= $task['task_id'] ?>"
                                            data-reviewee-id="<?= $activeTab === 'published' ? $task['runner_id'] : $task['requester_id'] ?>"
                                            data-role="<?= $activeTab === 'published' ? 'runner' : 'requester' ?>"
                                            data-review-id="<?= isset($task['review_id']) ? $task['review_id'] : '' ?>"
                                            data-rating="<?= htmlspecialchars($task['rating'] ?? '') ?>"
                                            data-comment="<?= htmlspecialchars($task['comment'] ?? '') ?>">
                                            <?= getIcon('star') ?> 編輯評價
                                        </button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php else: // Accepted Tab 
                            ?>
                                <?php if ($task['status'] === 'completed'): ?>
                                    <?php if (empty($task['rating'])): ?>
                                        <button class="review-btn flex items-center gap-2 px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors"
                                            data-task-id="<?= $task['task_id'] ?>"
                                            data-reviewee-id="<?= $activeTab === 'published' ? $task['runner_id'] : $task['requester_id'] ?>"
                                            data-role="<?= $activeTab === 'published' ? 'runner' : 'requester' ?>">
                                            <?= getIcon('star') ?> 評價<?= $activeTab === 'published' ? '執行者' : '發布者' ?>
                                        </button>
                                    <?php else: ?>
                                        <button class="review-btn flex items-center gap-2 px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors"
                                            data-task-id="<?= $task['task_id'] ?>"
                                            data-reviewee-id="<?= $activeTab === 'published' ? $task['runner_id'] : $task['requester_id'] ?>"
                                            data-role="<?= $activeTab === 'published' ? 'runner' : 'requester' ?>"
                                            data-review-id="<?= isset($task['review_id']) ? $task['review_id'] : '' ?>"
                                            data-rating="<?= htmlspecialchars($task['rating'] ?? '') ?>"
                                            data-comment="<?= htmlspecialchars($task['comment'] ?? '') ?>">
                                            <?= getIcon('star') ?> 編輯評價
                                        </button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Include the review modal component -->
<?php include __DIR__ . '/reviewModal.php'; ?>