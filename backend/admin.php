<?php
require_once "db.php";
require_once "auth.php";

// 算出總用戶數
function adminCountTotalUsers() {
    if (!isAdmin()) {
        throw new Exception("非管理員無權限");
    }
    global $pdo;
    $stmt = $pdo->query("SELECT count(user_id) as total from users");
    $row = $stmt->fetch();
    return $row['total'];
}

// 算出正在進行中任務數
function adminCountTasksInProgress() {
    if (!isAdmin()) {
        throw new Exception("非管理員無權限");
    }
    global $pdo;
    $stmt = $pdo->query("SELECT count(task_id) as total from tasks where status='in_progress'");
    $row = $stmt->fetch();
    return $row['total'];
}

// 算出今天完成任務件數
function adminCountTasksCompleteToday() {
    if (!isAdmin()) {
        throw new Exception("非管理員無權限");
    }
    global $pdo;
    $stmt = $pdo->query("SELECT count(task_id) as total from tasks where status='completed' AND DATE(completed_at)=CURDATE()");
    $row = $stmt->fetch();
    return $row['total'];
}

// 算出個別任務占比 (管理員專用)
// usage: $tasksCnt = adminGetTasksCount();
function adminGetTasksCount() {
    if (!isAdmin()) {
        throw new Exception("非管理員無權限");
    }
    global $pdo;
    $stmt = $pdo->query ("SELECT t.tags AS tag, COUNT(t.task_id) AS num, SUM(COUNT(t.task_id)) OVER() AS Total FROM Tasks t GROUP BY t.tags;");
    return $stmt->fetchAll();
}

// Ban 或 Unban 使用者 (管理者專用)
// usage: $users = adminSetUserStatus($user_id, $status)
function adminSetUserStatus($user_id, $status) {
    if (!isAdmin()) {
        throw new Exception("非管理員無權限");
    }
    global $pdo;
    $stmt = $pdo->prepare("UPDATE Users as u SET u.is_banned = ? WHERE u.user_id=?");
    $stmt->execute([$status, $user_id]);
}

// 取得所有任務（管理員專用）
// usage: $tasks = adminGetAllTasks();
function adminGetAllTasks()
{
    if (!isAdmin()) {
        throw new Exception("非管理員無權限");
    }
    global $pdo;
    $stmt = $pdo->query("SELECT t.*, u.name AS requester_name, 
                                            r.name AS runner_name
                         FROM Tasks t
                         LEFT JOIN Users u ON u.user_id = t.requester_id
                         LEFT JOIN Users r ON r.user_id = t.runner_id
                         ORDER BY created_at DESC");
    return $stmt->fetchAll();
}

// 取得所有使用者（管理員專用）
// usage: $users = adminGetAllUsers();
function adminGetAllUsers()
{
    if (!isAdmin()) {
        throw new Exception("非管理員無權限");
    }
    global $pdo;
    $stmt = $pdo->query("SELECT u.*, COALESCE(requester_stats.total_tasks, 0) AS tasksAsRequester, COALESCE(runner_stats.total_tasks, 0) AS tasksAsRunner
                                FROM Users u
                                LEFT JOIN ( SELECT requester_id, COUNT(*) AS total_tasks FROM Tasks GROUP BY requester_id ) AS requester_stats ON u.user_id = requester_stats.requester_id
                                LEFT JOIN ( SELECT runner_id, COUNT(*) AS total_tasks FROM Tasks GROUP BY runner_id ) AS runner_stats ON u.user_id = runner_stats.runner_id
                                order by u.user_id asc;");
    return $stmt->fetchAll();
}

// 刪除任務（管理員專用）
// usage: adminDeleteTask($task_id);
function adminDeleteTask($task_id)
{
    if (!isAdmin()) {
        throw new Exception("非管理員無權限");
    }
    global $pdo;
    // 因為有外鍵關聯，所以刪除任務會一併刪除相關的申請紀錄與評價
    $stmt = $pdo->prepare("DELETE FROM Reviews
                                WHERE task_id = ?");
    $stmt->execute([$task_id]);
    $stmt = $pdo->prepare("DELETE FROM Tasks
                                  WHERE task_id = ?;");
    return $stmt->execute([$task_id]);
}