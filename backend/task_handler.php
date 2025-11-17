<?php
session_start();
require_once "auth.php";
require_once "task.php";

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../public/index.php");
    exit;
}

$action = $_POST['action'] ?? '';
$task_id = $_POST['task_id'] ?? '';
$runner_id = $_POST['runner_id'] ?? '';
$source = $_POST['source'] ?? 'index';

$user_id = $_SESSION['user']['user_id'];

if (!$task_id) {
    header("Location: ../public/index.php");
    exit;
}

// 獲取任務資訊以檢查權限
$task = getTaskById($task_id);
if (!$task) {
    header("Location: ../public/index.php");
    exit;
}

try {
    switch ($action) {
        case 'approve':
            // 只有 requester 可以批准
            if ($task['requester_id'] != $user_id) {
                throw new Exception("無權限批准此任務");
            }
            if ($task['status'] !== 'confirming') {
                throw new Exception("任務狀態不允許批准");
            }
            approveTask($task_id, $runner_id);
            $_SESSION['message'] = '任務申請已批准！';
            break;

        case 'reject':
            // 只有 requester 可以拒絕
            if ($task['requester_id'] != $user_id) {
                throw new Exception("無權限拒絕此任務");
            }
            if ($task['status'] !== 'confirming') {
                throw new Exception("任務狀態不允許拒絕");
            }
            rejectTask($task_id, $user_id);
            $_SESSION['message'] = '任務申請已拒絕！';
            break;

        case 'complete_by_runner':
            // 只有 runner 可以完成任務
            if ($task['runner_id'] != $user_id) {
                throw new Exception("無權限完成此任務");
            }
            if ($task['status'] !== 'in_progress') {
                throw new Exception("任務狀態不允許完成");
            }
            completeTask($task_id, $user_id);
            break;

        case 'complete_by_requester':
            // 只有 requester 可以確認完成
            if ($task['requester_id'] != $user_id) {
                throw new Exception("無權限確認完成此任務");
            }
            if ($task['status'] !== 'in_progress') {
                throw new Exception("任務狀態不允許確認完成");
            }
            completeTask($task_id, $task['runner_id']);
            break;

        case 'cancel':
            // 只有 requester 可以取消
            if ($task['requester_id'] != $user_id) {
                throw new Exception("無權限取消此任務");
            }
            if (!in_array($task['status'], ['open', 'confirming'])) {
                throw new Exception("任務狀態不允許取消");
            }
            cancelTask($task_id, $user_id);
            break;

        default:
            throw new Exception("未知動作");
    }

    // 成功後重定向
    if ($source === 'myTask') {
        header("Location: ../public/index.php?tab=mytasks");
    } elseif ($source === 'task_detail') {
        header("Location: ../public/task_detail.php?task_id=$task_id");
    } else {
        header("Location: ../public/index.php");
    }
    exit;
} catch (Exception $e) {
    // 錯誤處理，可以記錄日誌或顯示錯誤
    $_SESSION['error'] = $e->getMessage();
    if ($source === 'myTask') {
        header("Location: ../public/index.php?tab=mytasks");
    } elseif ($source === 'task_detail') {
        header("Location: ../public/task_detail.php?task_id=$task_id");
    } else {
        header("Location: ../public/index.php");
    }
    exit;
}
