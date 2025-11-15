<?php
// session_start();
require_once "db.php";
require_once "user.php"; // 這行讓 findUserByEmail 可用

function loginUser($email, $password)
{
    $user = findUserByEmail($email);
    if (!$user)
        return false;
    if (!password_verify($password, $user["password_hash"]))
        return false;

    $_SESSION["user"] = $user;
    return true;
}

function requireLogin()
{
    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit;
    }
}

function isAdmin()
{
    return isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1;
}

function logoutUser()
{
    session_destroy();
    header("Location: login.php");
    exit;
}
