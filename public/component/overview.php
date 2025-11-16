<div id="tab-content-overview">

    <!-- 圖表 -->
    <!-- <div class="bg-white rounded-lg border border-gray-200 p-6">
        <h3 class="text-gray-900 mb-4">活動趨勢</h3>
        <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
            <p class="text-gray-600">圖表區域 - 可整合 Recharts</p>
        </div>
    </div> -->

    <!-- Categories -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <h3 class="text-gray-900 mb-4">熱門任務類別</h3>

        <div class="space-y-3">
            <?php
            $categories = ["代購","取件","送件","其他"];
            foreach ($categories as $category):
                $count = count(array_filter($tasks, fn($t) => $t['category'] === $category));
                $percentage = count($tasks) > 0 ? $count / count($tasks) * 100 : 0;
            ?>
            <div class="flex items-center justify-between">
                <span class="text-gray-700"><?= $category ?></span>
                <div class="flex items-center gap-3">
                    <div class="w-32 h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-blue-600 rounded-full" style="width: <?= $percentage ?>%"></div>
                    </div>
                    <span class="text-gray-600 w-12 text-right"><?= intval($percentage) ?>%</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        將上方 Categories 放入這裡形成好看版面

        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-gray-900 mb-4">最新系統通知</h3>

            <div class="space-y-3">

                <?php foreach ($tasks as $task): ?>
                    <?php if ($task['status'] === "disputed"): ?>
                        <div class="flex items-start gap-3 p-3 bg-yellow-50 rounded-lg">
                            <div class="w-5 h-5 text-yellow-600">⚠</div>
                            <div>
                                <p class="text-gray-900">任務「<?= $task['title'] ?>」有爭議需要處理</p>
                                <p class="text-gray-600"><?= $task['reportCount'] ?> 個檢舉</p>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>

                <div class="flex items-start gap-3 p-3 bg-green-50 rounded-lg">
                    <div class="w-5 h-5 text-green-600">✔</div>
                    <div>
                        <p class="text-gray-900">
                            新用戶註冊：<?= $users[count($users)-1]['name'] ?? '' ?>
                        </p>
                        <p class="text-gray-600">最近加入</p>
                    </div>
                </div>

            </div>
        </div>
    </div> -->
    
</div>