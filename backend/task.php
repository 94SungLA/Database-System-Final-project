<?php
require_once "db.php";

// 取得所有狀態為 open 的任務(不用POST、GET參數)
function getOpenTasks()
{
    global $pdo;
    $stmt = $pdo->query("SELECT t.*, u.name AS requester_name, u.rating_as_requester
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
    $stmt = $pdo->prepare("SELECT t.*, rn.name as runner_name, rq.name as requester_name FROM Tasks as t
                                  LEFT JOIN Users as rn on t.runner_id = rn.user_id
                                  LEFT JOIN Users as rq on t.requester_id = rq.user_id
                                  WHERE t.task_id=?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// 建立新任務
// usage: createTask($title, $desc, $tags, $reward, $deadline, $requester_id);
function createTask($title, $desc, $tags, $reward, $deadline, $location_tags, $requester_id)
{
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO Tasks (title, description, tags, reward, deadline, location_tags, requester_id)
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    return $stmt->execute([$title, $desc, $tags, $reward, $deadline, $location_tags, $requester_id]);
}

// 透過請求者ID取得其所有任務
// usage: $tasks = getTasksByRequesterID($_SESSION['user']['user_id']);
function getTasksByRequesterID($user_id)
{
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT t.*, 
               u_runner.name AS runner_name, 
               r.review_id, r.rating, r.comment
        FROM Tasks t
        LEFT JOIN Users u_runner ON t.runner_id = u_runner.user_id
        LEFT JOIN Reviews r ON t.task_id = r.task_id AND r.reviewer_id = ? AND r.role = 'runner'
        WHERE t.requester_id = ?
        ORDER BY t.created_at DESC
    ");
    $stmt->execute([$user_id, $user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 透過執行者ID取得其所有任務
// usage: $tasks = getTasksByRunnerID($_SESSION['user']['user_id']);
function getTasksByRunnerID($user_id)
{
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT t.*, 
               u_requester.name AS requester_name, 
               r.review_id, r.rating, r.comment
        FROM Tasks t
        LEFT JOIN Users u_requester ON t.requester_id = u_requester.user_id
        LEFT JOIN Reviews r ON t.task_id = r.task_id AND r.reviewer_id = ? AND r.role = 'requester'
        WHERE t.runner_id = ?
        ORDER BY t.created_at DESC
    ");
    $stmt->execute([$user_id, $user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                                  SET status = 'completed',
                                  completed_at = NOW()
                                  WHERE task_id = ? AND runner_id = ?;");
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

// 取得單筆任務詳細資訊
// usage: $task = getTaskDetailsById($_GET['id']);
function getTaskDetailsById($id)
{
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT t.*,
               req.name AS requester_name,
               req.phone AS requester_phone,
               req.email AS requester_email,
               req.rating_as_requester,
               run.name AS runner_name,
               run.phone AS runner_phone,
               run.email AS runner_email
        FROM Tasks t
        JOIN Users req ON req.user_id = t.requester_id
        LEFT JOIN Users run ON run.user_id = t.runner_id
        WHERE t.task_id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}
