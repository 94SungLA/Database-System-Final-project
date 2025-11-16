<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../../backend/auth.php";
require_once "../../backend/task.php";
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$taskId = $_POST['task_id'] ?? null;
if (!$taskId) {
    echo "Invalid task ID";
    exit;
}

$userId = $_SESSION['user']['user_id'];

try {
    applyTask($taskId, $userId);
    header('Location: ../index.php?tab=taskboard&task_id=' . $taskId);
} catch (Exception $e) {
    echo $e->getMessage();
}
