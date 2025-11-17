<?php
    require_once "../backend/admin.php";
    require_once "../backend/user.php";
    $users = adminGetAllUsers();

    // 給出 特定使用者 到彈出視窗 user-detail 內
    $selectedUser = null;
    $userTaskRequesterComplete = 0;
    $userTaskRunnerComplete = 0;
    if (isset($_POST['viewUserDetail'])) {
        $userTaskRequesterComplete = $_POST['userTaskRequesterComplete'];
        $userTaskRunnerComplete = $_POST['userTaskRunnerComplete'];
        $selectedUser = getUserById($_POST['user_id']);
    }

    // 在彈出視窗 user-detail 內 ban OR unban user
    if (isset($_POST['banAndUnbanInUserDetail'])) {
        $status = ($_POST['action'] === 'ban') ? 1 : 0;
        adminSetUserStatus($_POST['user_id'], $status);
        $users = adminGetAllUsers();
        $selectedUser = null;
    }

    // ban OR unban user
    if (isset($_POST['banAndUnban'])) {
        $status = ($_POST['action'] === 'ban') ? 1 : 0;
        adminSetUserStatus($_POST['user_id'], $status);
        $users = adminGetAllUsers();
    }

    // 關閉彈出視窗 user-detail
    if (isset($_POST['closeUserDetail'])) {
        $selectedUser = null;
    }

    $ratingAsNull = '(*´･д･)?';
    $adminRating = 'OVOb';
?>

<!-- Users Tab -->
<div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto whitespace-nowrap ">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-gray-900">用戶名稱</th>
                    <th class="px-6 py-3 text-left text-gray-900">
                        <div class="inline-block bg-green-300 px-2 py-1 rounded-lg">
                            工具人
                        </div>
                        評分
                    </th>
                    <th class="px-6 py-3 text-left text-gray-900">
                        <div class="inline-block bg-blue-300 px-2 py-1 rounded-lg">
                            委託人
                        </div>
                        評分
                    </th>
                    <th class="px-6 py-3 text-left text-gray-900">完成任務</th>
                    <th class="px-6 py-3 text-left text-gray-900">發布任務</th>
                    <th class="px-6 py-3 text-left text-gray-900">狀態</th>
                    <th class="px-6 py-3 text-left text-gray-900">註冊日期</th>
                    <th class="px-6 py-3 text-left text-gray-900">操作</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($users as $user): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-gray-900"><?= $user['name'] ?>
                            <?php if ($user['is_admin']): ?>
                                <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded-full text-sm">
                                    管理員
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-left text-gray-700">
                            <div class="px-6 py-4 flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-yellow-500">
                                    <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                                </svg>
                                <?php if ($user['is_admin']): ?>
                                    <?= $adminRating ?>
                                <?php else: ?>
                                    <?= $user['rating_as_runner'] !== null ? $user['rating_as_runner'] : $ratingAsNull ?>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-left text-gray-700">
                            <div class="px-6 py-4 flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-yellow-500">
                                    <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                                </svg>
                                <?php if ($user['is_admin']): ?>
                                    <?= $adminRating ?>
                                <?php else: ?>
                                    <?= $user['rating_as_requester'] !== null ? $user['rating_as_requester'] : $ratingAsNull ?>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-700"><?= $user['tasksAsRunner'] ?></td>
                        <td class="px-6 py-4 text-gray-700"><?= $user['tasksAsRequester'] ?></td>
                        <td class="px-6 py-4">
                            <?php
                                if ($user['is_banned'] === 0) {
                                    $statusClass = "bg-green-100 text-green-700";
                                    $statusText = "正常";
                                } else {
                                    $statusClass = "bg-red-100 text-red-700";
                                    $statusText = "停權";
                                }
                            ?>
                            <span class="px-3 py-1 rounded-full <?= $statusClass ?>">
                                <?= $statusText ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-700"><?= $user['created_at'] ?></td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                    <input type="hidden" name="userTaskRequesterComplete" value="<?= $user['tasksAsRequester'] ?>">
                                    <input type="hidden" name="userTaskRunnerComplete" value="<?= $user['tasksAsRunner'] ?>">
                                    <button name="viewUserDetail" class="text-blue-600 hover:text-blue-700">查看</button>
                                </form>
                                <?php if($user['is_admin'] != true): ?>
                                    <?php if ($user['is_banned'] == 0): ?>
                                        <!-- 停權 -->
                                        <form method="POST" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']); ?>" style="display: contents;">
                                            <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                            <input type="hidden" name="action" value="ban">
                                            <button name="banAndUnban" class="text-red-600 hover:text-red-700">停權</button>
                                        </form>
                                    <?php else: ?>
                                        <!-- 解除 -->
                                        <form method="POST" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']); ?>" style="display: contents;">
                                            <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                            <input type="hidden" name="action" value="unban">
                                            <button name="banAndUnban" class="text-green-600 hover:text-green-700">解除</button>
                                        </form>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- user in detail -->
<?php if (!empty($selectedUser)): ?>
<div class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg max-w-2xl w-full max-h-[70vh] overflow-y-auto">
        <div class="p-6">
            <!-- Header -->
            <div class="flex justify-between items-start pb-2 mb-4 border-b border-gray-200">
                <div>
                    <h2 class="text-gray-900 mb-2">用戶詳情</h2>
                    <p class="text-gray-600">查看用戶完整資訊</p>
                </div>
                <form method="POST" style="display:inline;">
                    <button name="closeUserDetail" class="text-3xl text-gray-400 hover:text-4xl hover:font-bold hover:text-red-600 transition-all duration-200">&times;</button>
                </form>
            </div>

            <div class="space-y-6">
                <!-- User Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-4">
                        <div>
                            <p class="text-gray-600 mb-1">姓名</p>
                            <p class="text-gray-900"><?= htmlspecialchars($selectedUser['name']) ?></p>
                        </div>
                        <div>
                            <p class="text-gray-600 mb-1">身份</p>
                            <?php if ($selectedUser['is_admin']): ?>
                                <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded-full text-sm">
                                    管理員
                                </span>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-sm">
                                    工具人
                                </span>
                                <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">
                                    委託人
                                </span>
                            <?php endif; ?>
                        </div>
                        <div>
                            <p class="text-gray-600 mb-1">狀態</p>
                            <span class="inline-block px-3 py-1 rounded-full <?= $selectedUser['is_banned'] ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?>">
                                <?= $selectedUser['is_banned'] ? '停權' : '正常' ?>
                            </span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                            <div>
                                <p class="text-gray-600">電子信箱</p>
                                <p class="text-gray-900"><?= htmlspecialchars($selectedUser['email']) ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                            </svg>
                            <div>
                                <p class="text-gray-600">電話</p>
                                <p class="text-gray-900"><?= htmlspecialchars($selectedUser['phone']) ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                            </svg>
                            <div>
                                <p class="text-gray-600">註冊日期</p>
                                <p class="text-gray-900"><?= htmlspecialchars($selectedUser['created_at']) ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-3 gap-4 pt-6 border-t border-gray-200">
                    <div class="text-center">
                        <div class="flex flex-col items-center justify-center gap-1 mb-1">
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-sm">
                                工具人 : <?= $selectedUser['rating_as_runner'] !== null ? $selectedUser['rating_as_runner'] : $ratingAsNull ?>
                            </span>
                            <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">
                                委託人 : <?= $selectedUser['rating_as_requester'] !== null ? $selectedUser['rating_as_requester'] : $ratingAsNull ?>
                            </span>
                        </div>
                        <p class="text-gray-600">評分</p>
                    </div>
                    <div class="text-center">
                        <p class="text-gray-900"><?= $userTaskRunnerComplete ?></p>
                        <p class="text-gray-600">完成任務</p>
                    </div>
                    <div class="text-center">
                        <p class="text-gray-900"><?= $userTaskRequesterComplete ?></p>
                        <p class="text-gray-600">發布任務</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-3 pt-6 border-t border-gray-200">
                    <?php $showBanButton = ($selectedUser['is_admin'] != true); ?>
                    <?php if ($showBanButton): ?>
                        <!-- 左側停權/解除停權按鈕 -->
                        <form method="POST" style="flex:1;">
                            <input type="hidden" name="user_id" value="<?= $selectedUser['user_id'] ?>">
                            <?php if ($selectedUser['is_banned'] == 0): ?>
                                <input type="hidden" name="action" value="ban">
                                <button 
                                    name="banAndUnbanInUserDetail"
                                    class="w-full h-full py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 flex items-center justify-center"
                                >
                                    停權此用戶
                                </button>
                            <?php else: ?>
                                <input type="hidden" name="action" value="unban">
                                <button 
                                    name="banAndUnbanInUserDetail"
                                    class="w-full h-full py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 flex items-center justify-center"
                                >
                                    解除停權
                                </button>
                            <?php endif; ?>
                        </form>
                    <?php endif; ?>

                    <!-- 右側關閉按鈕，若沒有左側按鈕則自動滿版 -->
                    <form method="POST" style="<?= $showBanButton ? 'flex:1;' : 'flex:1 0 100%;' ?>">
                        <button
                            name="closeUserDetail"
                            class="w-full py-2 rounded-lg bg-gray-200 text-gray-900 hover:bg-gray-300 text-center"
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
