<?php
    require_once "../backend/admin.php";
    require_once "../backend/task.php";
    // 假設 $tasks 來自資料庫查詢，例如：
    $tasks = adminGetAllTasks();

    $statusColors = [
        'open' => 'bg-yellow-500',
        'confirming' => 'bg-violet-500',
        'in_progress' => 'bg-blue-500',
        'completed' => 'bg-green-500',
        'cancelled' => 'bg-gray-500',
    ];

    function getStatusLabel($status) {
        return match($status) {
            'open' => '待接單',
            'confirming' => '待確認',
            'in_progress' => '進行中',
            'completed' => '已完成',
            'cancelled' => '已取消',
            default => '未知',
        };
    }

    // 彈出視窗 task-detail
    $selectedTask = null;
    if (isset($_POST['viewTaskDetail'])) {
        $selectedTask = getTaskAndCommentById($_POST['task_id']);
    }

    // 關閉彈出視窗 task-detail
    if (isset($_POST['closeTaskDetail'])) {
        $selectedTask = null;
    }

    // 取消任務 (彈出視窗中)
    if (isset($_POST['adminCancelTask'])) {
        cancelTask($_POST['task_id'], $_POST['requester_id']);
        $tasks = adminGetAllTasks();
        $selectedTask = null;
    }

    // 刪除任務紀錄
    if (isset($_POST['deleteTaskByTaskId'])) {
        adminDeleteTask($_POST['task_id']);
        $tasks = adminGetAllTasks();
    }

    // 刪除以 requester/runner 評論 runner/requester 之評論
    if (isset($_POST['adminDeleteReview'])) {
       adminDeleteReview($_POST['review_id'], $_POST['role']);
       $selectedTask = getTaskAndCommentById($_POST['task_id']);
    }

    $noRunner = 'QAQ';
?>

<div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto whitespace-nowrap">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-gray-900">任務標題</th>
                    <th class="px-6 py-3 text-left text-gray-900">類別</th>
                    <th class="px-6 py-3 text-left text-gray-900">委託人</th>
                    <th class="px-6 py-3 text-left text-gray-900">工具人</th>
                    <th class="px-6 py-3 text-left text-gray-900">狀態</th>
                    <th class="px-6 py-3 text-left text-gray-900">酬勞</th>
                    <th class="px-6 py-3 text-left text-gray-900">操作</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach($tasks as $task): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-gray-900"><?= htmlspecialchars($task['title']) ?></td>
                    <td class="px-6 py-4 text-gray-700"><?= htmlspecialchars($task['tags']) ?></td>
                    <td class="px-6 py-4 text-gray-700"><?= htmlspecialchars($task['requester_name']) ?></td>
                    <td class="px-6 py-4 text-gray-700">
                        <span class="<?= $task['runner_name'] ? '' : 'italic text-gray-400' ?>">
                            <?= $task['runner_name'] ?: $noRunner ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-white <?= $statusColors[$task['status']] ?? 'bg-gray-300' ?>">
                            <?= getStatusLabel($task['status']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-700">NT$ <?= number_format($task['reward']) ?></td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="task_id" value="<?= $task['task_id'] ?>">
                                <button name="viewTaskDetail" class="text-blue-600 hover:text-blue-700">查看</button>
                            </form>
                            <!-- 刪除 -->
                            <form method="POST" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']); ?>" style="display: contents;">
                                <input type="hidden" name="task_id" value="<?= $task['task_id'] ?>">
                                <button name="deleteTaskByTaskId" class="text-red-600 hover:text-red-700">刪除</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- taskDetail -->
<?php if (!empty($selectedTask)): ?>
  <div class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg max-w-2xl w-full max-h-[70vh] overflow-y-auto">
      <div class="p-6">

        <!-- Header -->
        <div class="flex justify-between items-start pb-2 mb-4 border-b border-gray-200">
          <div>
            <h2 class="text-gray-900 mb-2">任務詳情</h2>
            <p class="text-gray-600">查看任務完整資訊</p>
          </div>
          <form method="POST" style="display:inline;">
              <button name="closeUserDetail" class="text-3xl text-gray-400 hover:text-4xl hover:font-bold hover:text-red-600 transition-all duration-200">&times;</button>
          </form>
        </div>

        <div class="space-y-6">
          <!-- Task Info -->
          <div class="pb-2 mb-2 border-b border-gray-200">
            <h3 class="text-gray-900 mb-2">
              <?= htmlspecialchars($selectedTask['title']) ?>
            </h3>
            <p class="text-gray-700">
              <?= nl2br(htmlspecialchars($selectedTask['description'])) ?>
            </p>
          </div>

          <!-- Grid Info -->
          <div class="grid grid-cols-2 gap-4">

            <div>
              <p class="text-gray-600 mb-1">類別</p>
              <p class="text-gray-900"><?= htmlspecialchars($selectedTask['tags']) ?></p>
            </div>

            <div>
              <p class="text-gray-600 mb-1">酬勞</p>
              <p class="text-gray-900">NT$ <?= htmlspecialchars($selectedTask['reward']) ?></p>
            </div>

            <div>
              <p class="text-gray-600 mb-1">委託人</p>
              <p class="text-gray-900"><?= htmlspecialchars($selectedTask['requester_name']) ?></p>
            </div>

            <div>
              <p class="text-gray-600 mb-1">工具人</p>
              <p class="text-gray-900">
                <?= $selectedTask['runner_name'] ? htmlspecialchars($selectedTask['runner_name']) : $noRunner ?>
              </p>
            </div>

            <div>
              <p class="text-gray-600 mb-1">建立時間</p>
              <p class="text-gray-900"><?= htmlspecialchars($selectedTask['created_at']) ?></p>
            </div>

            <div>
              <p class="text-gray-600 mb-1">截止時間</p>
              <p class="text-gray-900"><?= htmlspecialchars($selectedTask['deadline']) ?></p>
            </div>

            <div>
              <p class="text-gray-600 mb-1">狀態</p>
              <span class="px-3 py-1 rounded-full text-white <?= $statusColors[$selectedTask['status']] ?? 'bg-gray-300' ?>">
                  <?= getStatusLabel($selectedTask['status']) ?>
              </span>
            </div>

            <div>
              <p class="text-gray-600 mb-1">地點</p>
              <p class="text-gray-900"><?= htmlspecialchars($selectedTask['location_tags']) ?></p>
            </div>
          </div>

          <!-- Comment -->
          <!-- 委託人評論 -->
          <?php if (!empty($selectedTask['rq_review']) || !empty($selectedTask['rn_review'])): ?>
            <div class="flex flex-col pt-6 border-t border-gray-200">
              <?php if (!empty($selectedTask['rn_review'])): ?>
                <div class="mb-4 p-3 bg-yellow-50 rounded-lg w-full">
                    <div class="flex items-center mb-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        <span class="text-gray-900 font-semibold"><span class="bg-blue-300 rounded-lg pl-1 pr-1 ml-1 mr-1">委託人: <?= htmlspecialchars($selectedTask['requester_name']) ?></span>給出的評價：<?= htmlspecialchars($selectedTask['rnrt']) ?> / 5</span>
                    </div>
                    <p class="text-gray-700"><?= htmlspecialchars($selectedTask['rn_review']) ?></p>
                    <form method="POST" class="flex flex-1 justify-end">
                      <input type="hidden" name="task_id" value="<?= $selectedTask['task_id'] ?>">
                      <input type="hidden" name="review_id" value="<?= $selectedTask['rnrwid'] ?>">
                      <input type="hidden" name="role" value="runner">
                      <button
                          name="adminDeleteReview"
                          class="pl-2 pr-2 rounded-lg bg-red-200 text-gray-900 hover:bg-red-300 text-center text-base"
                          style="border:none; cursor:pointer;"
                      >
                          刪除
                      </button>
                    </form>
                </div>
              <?php endif; ?>
              <!-- 工具人評論 -->
              <?php if (!empty($selectedTask['rq_review'])): ?>
                <div class="mb-4 p-3 bg-yellow-50 rounded-lg w-full">
                    <div class="flex items-center mb-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        <span class="text-gray-900 font-semibold"><span class="bg-green-300 rounded-lg pl-1 pr-1 ml-1 mr-1">工具人: <?= htmlspecialchars($selectedTask['runner_name']) ?></span>給出的評價：<?= htmlspecialchars($selectedTask['rqrt']) ?> / 5</span>
                    </div>
                    <p class="text-gray-700"><?= htmlspecialchars($selectedTask['rq_review']) ?></p>
                    <form method="POST" class="flex flex-1 justify-end">
                      <input type="hidden" name="task_id" value="<?= $selectedTask['task_id'] ?>">
                      <input type="hidden" name="review_id" value="<?= $selectedTask['rqrwid'] ?>">
                      <input type="hidden" name="role" value="requester">
                      <button
                          name="adminDeleteReview"
                          class="pl-2 pr-2 rounded-lg bg-red-200 text-gray-900 hover:bg-red-300 text-center text-base"
                          style="border:none; cursor:pointer;"
                      >
                          刪除
                      </button>
                    </form>
                </div>
              <?php endif; ?>
            </div>
          <?php endif; ?>

          <!-- Actions -->
          <div class="flex gap-3 pt-6 border-t border-gray-200">

            <!-- 保留取消任務 -->
            <?php if (in_array($selectedTask['status'], ['open', 'confirming'])): ?>
                  <form method="POST" class="flex-1">
                      <input type="hidden" name="task_id" value="<?= $selectedTask['task_id'] ?>">
                      <input type="hidden" name="requester_id" value="<?= $selectedTask['requester_id'] ?>">
                      <button
                          name="adminCancelTask"
                          class="w-full bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 transition-colors"
                      >
                          取消任務
                      </button>
                  </form>
              <?php endif; ?>

            <form method="POST" style="display:inline-flex; flex:1;">
              <button
                  name="closeTaskDetail"
                  class="w-full py-2 rounded-lg bg-gray-200 text-gray-900 hover:bg-gray-300 text-center"
                  style="border:none; cursor:pointer;"
              >
                  關閉
              </button>
          </form>

          </div>

        </div>
      </div>
    </div>
  </div>
<?php endif; ?>