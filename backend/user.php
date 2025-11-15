<?php
require_once "db.php";

function findUserByEmail($email)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE email=?");
    $stmt->execute([$email]);
    return $stmt->fetch();
}

function createUser($name, $email, $password, $phone)
{
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO Users (name, email, password_hash, phone) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$name, $email, password_hash($password, PASSWORD_BCRYPT), $phone]);
}

function getUserById($id)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE user_id=?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function updateUserProfile($user_id, $name, $phone, $email)
{
    global $pdo;

    // email 改掉時避免與他人重複
    $sql = "SELECT user_id FROM Users WHERE email = ? AND user_id != ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email, $user_id]);
    if ($stmt->fetch()) {
        return "email_taken"; // 代表 email 已被別人使用
    }

    // 更新資料
    $sql = "UPDATE Users
            SET name = ?, phone = ?, email = ?
            WHERE user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $phone, $email, $user_id]);

    // 更新 session 中的資料（保持同步）
    $_SESSION["user"]["name"] = $name;
    $_SESSION["user"]["phone"] = $phone;
    $_SESSION["user"]["email"] = $email;

    return "success";
}
