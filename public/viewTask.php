<?php
require_once "../backend/auth.php";
requireLogin();
require_once "../backend/task.php";

$task = getTaskById($_GET["id"]);
// 權限檢查
if (!$task || ($task['requester_id'] != $_SESSION['user']['user_id'] && $task['runner_id'] != $_SESSION['user']['user_id'])) {
    header("Location: index.php");
    exit;
}
?>
<h1><?= $task["title"] ?></h1>
<p><?= $task["description"] ?></p>
<p>酬勞：<?= $task["reward"] ?></p>
<p>截止：<?= $task["deadline"] ?></p>