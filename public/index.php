<?php
require_once "../backend/auth.php";
requireLogin();
require_once "../backend/task.php";

$tasks = getOpenTasks();
?>
<h1>可接任務列表</h1>
<?php foreach ($tasks as $t): ?>
    <div>
        <b><?= $t["title"] ?></b> | <?= $t["reward"] ?> 元 | by <?= $t["requester_name"] ?>
        <a href="viewTask.php?id=<?= $t["task_id"] ?>">查看</a>
    </div>
<?php endforeach; ?>
