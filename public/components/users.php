<?php
    require_once "../backend/admin.php";
    $users = adminGetAllUsers();
    echo '<pre>';
    var_dump($users);
    echo '</pre>';
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
                        <td class="px-6 py-4 text-gray-900"><?= $user['name'] ?></td>
                        <td class="px-6 py-4 text-left text-gray-700">
                            <div class="px-6 py-4 flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-yellow-500">
                                    <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                                </svg>
                                <?= $user['rating_as_runner'] ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-left text-gray-700">
                            <div class="px-6 py-4 flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-yellow-500">
                                    <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                                </svg>
                                <?= $user['rating_as_requester'] ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-700"><?= $user['review_count_runner'] ?></td>
                        <td class="px-6 py-4 text-gray-700"><?= $user['review_count_requester'] ?></td>
                        <td class="px-6 py-4"><?= $user['status'] ?></td>
                        <td class="px-6 py-4 text-gray-700"><?= $user['joinDate'] ?></td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <button class="text-blue-600 hover:text-blue-700">查看</button>
                                <button class="text-red-600 hover:text-red-700">停權</button>
                                <button class="text-green-600 hover:text-red-700">解除</button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>