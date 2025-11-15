<?php
require_once "db.php";

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

function getTaskById($id)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM Tasks WHERE task_id=?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function createTask($title, $desc, $tags, $reward, $deadline, $requester_id)
{
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO Tasks (title, description, tags, reward, deadline, requester_id)
                           VALUES (?, ?, ?, ?, ?, ?)");
    return $stmt->execute([$title, $desc, $tags, $reward, $deadline, $requester_id]);
}