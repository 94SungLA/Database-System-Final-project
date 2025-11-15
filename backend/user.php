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