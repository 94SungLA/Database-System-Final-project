<?php
require_once "db.php";

// 取得所有狀態為 open 的任務(不用POST、GET參數)
function getOpenTasks()
{
    global $pdo;
    $stmt = $pdo->query("SELECT t.*, u.name AS requester_name
                         FROM Tasks t
                         JOIN Users u ON u.user_id = t.requester_id
                         WHERE status='open'
                         ORDER BY created_at DESC");
    return $stmt->fetchAll();
}

// 透過任務ID取得任務詳細資訊
// usage: $task = getTaskById($_GET['id']);
function getTaskById($id)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM Tasks WHERE task_id=?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// 建立新任務
// usage: createTask($title, $desc, $tags, $reward, $deadline, $requester_id);
function createTask($title, $desc, $tags, $reward, $deadline, $requester_id)
{
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO Tasks (title, description, tags, reward, deadline, requester_id)
                           VALUES (?, ?, ?, ?, ?, ?)");
    return $stmt->execute([$title, $desc, $tags, $reward, $deadline, $requester_id]);
}

// 透過請求者ID取得其所有任務
// usage: $tasks = getTasksByRequesterID($_SESSION['user']['user_id']);
function getTasksByRequesterID($requester_id)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM Tasks WHERE requester_id=? ORDER BY created_at DESC");
    $stmt->execute([$requester_id]);
    return $stmt->fetchAll();
}

// 透過執行者ID取得其所有任務
// usage: $tasks = getTasksByRunnerID($_SESSION['user']['user_id']);
function getTasksByRunnerID($runner_id)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM Tasks WHERE runner_id=? ORDER BY created_at DESC");
    $stmt->execute([$runner_id]);
    return $stmt->fetchAll();
}

// runner 申請接取任務
// usage: applyTask($task_id, $_SESSION['user']['user_id']);
function applyTask($task_id, $runner_id)
{
    global $pdo;
    $stmt = $pdo->prepare("UPDATE Tasks 
                                  SET runner_id = ?, 
                                  status = 'confirming' 
                                  WHERE task_id = ? 
                                  AND status = 'open';");
    return $stmt->execute([$runner_id, $task_id]);
}

// requester 批准任務申請
// usage: approveTask($task_id, $runner_id);
function approveTask($task_id, $runner_id)
{
    global $pdo;
    $stmt = $pdo->prepare("UPDATE Tasks 
                                  SET status = 'in_progress' 
                                  WHERE task_id = ? 
                                  AND runner_id = ? 
                                  AND status = 'confirming';");
    return $stmt->execute([$task_id, $runner_id]);
}


// requester 拒絕任務申請
// usage: rejectTask($task_id, $_SESSION['user']['user_id']);
function rejectTask($task_id, $requester_id)
{
    global $pdo;
    $stmt = $pdo->prepare("UPDATE Tasks 
                                  SET runner_id = NULL, 
                                  status = 'open' 
                                  WHERE task_id = ? 
                                  AND requester_id = ? 
                                  AND status = 'confirming';");
    return $stmt->execute([$task_id, $requester_id]);
}

// runner 完成任務
// usage: completeTask($task_id, $_SESSION['user']['user_id']);
function completeTask($task_id, $runner_id)
{
    global $pdo;
    $stmt = $pdo->prepare("UPDATE Tasks
                                  SET status = 'completed'
                                  WHERE task_id = ? AND runner_id = ? AND status = 'in_progress';");
    return $stmt->execute([$task_id, $runner_id]);
}

// requester 取消任務
// usage: cancelTask($task_id, $_SESSION['user']['user_id']);
function cancelTask($task_id, $requester_id)
{
    global $pdo;
    $stmt = $pdo->prepare("UPDATE Tasks
                                  SET status = 'cancelled'
                                  WHERE task_id = ?
                                  AND requester_id = ?
                                  AND status IN ('open', 'confirming');");
    return $stmt->execute([$task_id, $requester_id]);
}