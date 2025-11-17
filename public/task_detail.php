<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>任務詳情</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">
    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    require_once "../backend/auth.php";
    require_once "../backend/task.php";
    require_once "../backend/review.php";
    requireLogin();

    $taskId = null;
    if (isset($_GET['task_id'])) {
        $taskId = (int)$_GET['task_id'];
    } elseif (isset($_GET['id'])) {
        $taskId = (int)$_GET['id'];
    } else {
        echo "<div class='text-red-600'>缺少任務參數。</div>";
        return;
    }

    $task = getTaskDetailsById($taskId);
    if (!$task) {
        echo "<div class='text-red-600'>找不到該任務。</div>";
        return;
    }

    // 獲取 requester 和 runner 的評分
    $requester_rating = getUserRating($task['requester_id'], 'requester');
    $runner_rating = !empty($task['runner_id']) ? getUserRating($task['runner_id'], 'runner') : null;

    $statusMap = [
        'open' => '開放中',
        'confirming' => '等待確認',
        'in_progress' => '執行中',
        'completed' => '已完成',
        'cancelled' => '已取消'
    ];
    ?>
    <div class="bg-gradient-to-br from-blue-50 via-indigo-50 to-white shadow-2xl rounded-2xl p-10 border border-gray-300 hover:shadow-3xl transition-all duration-500 transform hover:scale-105 relative">
        <div class="grid grid-cols-3 items-center mb-8">
            <button onclick="history.back()" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-300 shadow-lg justify-self-start">
                ← 回到上一頁
            </button>
            <h2 class="text-4xl font-extrabold text-gray-900 flex items-center animate-pulse text-center justify-self-center col-span-1">
                <span class="mr-4 text-5xl">📋</span>
                <?= htmlspecialchars($task['title']) ?>
            </h2>
            <div></div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-8 rounded-xl shadow-lg border-l-4 border-blue-500 hover:shadow-xl transition-shadow duration-300">
                <h3 class="font-bold text-gray-800 mb-6 flex items-center text-xl">
                    <span class="mr-3 text-2xl">ℹ️</span>
                    基本資訊
                </h3>
                <p class="mb-4"><span class="text-gray-700 font-semibold">狀態：</span><span class="inline-block px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-500 text-white rounded-full text-sm font-medium shadow-md animate-bounce"> <?= $statusMap[$task['status']] ?? $task['status'] ?> </span></p>
                <p class="mb-4"><span class="text-gray-700 font-semibold">標籤：</span><span class="text-blue-600 font-medium">🏷️ <?= htmlspecialchars($task['tags']) ?></span></p>
                <?php if (!empty($task['location_tags'])): ?>
                    <p class="mb-4"><span class="text-gray-700 font-semibold">位置：</span><span class="text-green-600 font-medium">📍 <?= htmlspecialchars($task['location_tags']) ?></span></p>
                <?php endif; ?>
                <p class="mb-4"><span class="text-gray-700 font-semibold">報酬：</span><span class="text-green-700 font-bold text-lg">$<?= number_format($task['reward'], 2) ?> 💰</span></p>
                <p class="mb-4"><span class="text-gray-700 font-semibold">截止：</span><span class="text-red-600 font-medium">⏰ <?= htmlspecialchars($task['deadline']) ?></span></p>
                <p><span class="text-gray-700 font-semibold">建立時間：</span><span class="text-gray-500">🕒 <?= htmlspecialchars($task['created_at']) ?></span></p>
            </div>
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-8 rounded-xl shadow-lg border-l-4 border-purple-500 hover:shadow-xl transition-shadow duration-300">
                <h3 class="font-bold text-gray-800 mb-6 flex items-center text-xl">
                    <span class="mr-3 text-2xl">👥</span>
                    雙方資訊
                </h3>
                <p class="mb-4"><span class="text-gray-700 font-semibold">發布者：</span><span class="text-indigo-700 font-medium">👤 <?= htmlspecialchars($task['requester_name']) ?> </span><span class="text-yellow-600 font-medium">⭐ <?= number_format($requester_rating['rating_as_requester'], 1) ?> (<?= $requester_rating['review_count_requester'] ?> 評價)</span></p>
                <p class="text-sm text-gray-600 mb-6">📧 <?= htmlspecialchars($task['requester_email']) ?> · 📞 <?= htmlspecialchars($task['requester_phone']) ?></p>
                <?php if (!empty($task['runner_id'])): ?>
                    <p class="mb-4"><span class="text-gray-700 font-semibold">執行者：</span><span class="text-teal-700 font-medium">🏃 <?= htmlspecialchars($task['runner_name']) ?> </span><span class="text-yellow-600 font-medium">⭐ <?= number_format($runner_rating['rating_as_runner'], 1) ?> (<?= $runner_rating['review_count_runner'] ?> 評價)</span></p>
                    <p class="text-sm text-gray-600 mb-4">📧 <?= htmlspecialchars($task['runner_email']) ?> · 📞 <?= htmlspecialchars($task['runner_phone']) ?></p>
                <?php else: ?>
                    <p class="text-gray-500 italic text-lg">尚未有人接取 😔</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="mt-10 bg-gradient-to-r from-gray-50 to-gray-100 p-8 rounded-xl shadow-lg border-l-4 border-green-500 hover:shadow-xl transition-shadow duration-300">
            <h3 class="font-bold text-gray-800 mb-6 flex items-center text-xl">
                <span class="mr-3 text-2xl">📝</span>
                任務描述
            </h3>
            <p class="whitespace-pre-line text-gray-900 leading-relaxed text-lg border-t pt-4 border-gray-300"><?= nl2br(htmlspecialchars($task['description'])) ?></p>
        </div>
        <div class="mt-10 bg-gradient-to-r from-yellow-50 to-yellow-100 p-8 rounded-xl shadow-lg border-l-4 border-yellow-500 hover:shadow-xl transition-shadow duration-300">
            <h3 class="font-bold text-gray-800 mb-6 flex items-center text-xl">
                <span class="mr-3 text-2xl">⭐</span>
                發布者評價
            </h3>
            <?php
            $reviews = getUserReviews($task['requester_id'], 'requester', 5);
            if (!empty($reviews)): ?>
                <ul class="space-y-4">
                    <?php foreach ($reviews as $review): ?>
                        <li class="bg-white p-4 rounded-lg shadow-sm border">
                            <div class="flex items-center mb-2">
                                <span class="text-yellow-500 font-semibold">⭐ <?= $review['rating'] ?>/5</span>
                                <span class="ml-4 text-gray-600">由 <?= htmlspecialchars($review['reviewer_name']) ?> 於 <?= htmlspecialchars($review['created_at']) ?></span>
                            </div>
                            <p class="text-gray-800"><?= htmlspecialchars($review['comment']) ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-gray-500 italic">尚未有評價。</p>
            <?php endif; ?>
        </div>
        <?php if (!empty($task['runner_id'])): ?>
            <div class="mt-10 bg-gradient-to-r from-cyan-50 to-cyan-100 p-8 rounded-xl shadow-lg border-l-4 border-cyan-500 hover:shadow-xl transition-shadow duration-300">
                <h3 class="font-bold text-gray-800 mb-6 flex items-center text-xl">
                    <span class="mr-3 text-2xl">🏃</span>
                    執行者評價
                </h3>
                <?php
                $runner_reviews = getUserReviews($task['runner_id'], 'runner', 5);
                if (!empty($runner_reviews)): ?>
                    <ul class="space-y-4">
                        <?php foreach ($runner_reviews as $review): ?>
                            <li class="bg-white p-4 rounded-lg shadow-sm border">
                                <div class="flex items-center mb-2">
                                    <span class="text-cyan-500 font-semibold">⭐ <?= $review['rating'] ?>/5</span>
                                    <span class="ml-4 text-gray-600">由 <?= htmlspecialchars($review['reviewer_name']) ?> 於 <?= htmlspecialchars($review['created_at']) ?></span>
                                </div>
                                <p class="text-gray-800"><?= htmlspecialchars($review['comment']) ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-gray-500 italic">尚未有評價。</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="mt-10 flex gap-6 justify-center">
            <?php if ($task['status'] === 'open' && $task['requester_id'] != $_SESSION['user']['user_id']): ?>
                <form method="POST" action="actions/apply_task.php" onsubmit="return confirm('確定要接這個任務？');">
                    <input type="hidden" name="task_id" value="<?= $taskId ?>">
                    <button type="submit" class="px-8 py-4 bg-gradient-to-r from-green-500 to-teal-500 text-white rounded-xl hover:from-green-600 hover:to-teal-600 transition-all duration-300 flex items-center shadow-lg transform hover:scale-110">
                        <span class="mr-3 text-xl">✅</span>
                        我要接
                    </button>
                </form>
            <?php endif; ?>
            <?php if ($task['status'] === 'confirming' && $task['requester_id'] == $_SESSION['user']['user_id']): ?>
                <form action="../backend/task_handler.php" method="POST" class="contents">
                    <input type="hidden" name="task_id" value="<?= $taskId ?>">
                    <input type="hidden" name="runner_id" value="<?= htmlspecialchars($task['runner_id'] ?? '') ?>">
                    <input type="hidden" name="source" value="task_detail">
                    <button type="submit" name="action" value="approve" class="px-8 py-4 bg-gradient-to-r from-green-500 to-teal-500 text-white rounded-xl hover:from-green-600 hover:to-teal-600 transition-all duration-300 flex items-center shadow-lg transform hover:scale-110">
                        <span class="mr-3 text-xl">✅</span>
                        批准申請
                    </button>
                    <button type="submit" name="action" value="reject" class="px-8 py-4 bg-gradient-to-r from-red-500 to-pink-500 text-white rounded-xl hover:from-red-600 hover:to-pink-600 transition-all duration-300 flex items-center shadow-lg transform hover:scale-110">
                        <span class="mr-3 text-xl">❌</span>
                        拒絕申請
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>