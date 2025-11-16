<?php
// session_start();
require_once "db.php";
require_once "user.php"; // 這行讓 findUserByEmail 可用

// 使用者登入
// usage: loginUser($email, $password);
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

// 檢查是否已登入，未登入則導向登入頁
// usage: requireLogin();
function requireLogin()
{
    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit;
    }
}

// 檢查是否為管理員
// usage: isAdmin();
function isAdmin()
{
    return isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1;
}

// 使用者登出
// usage: logoutUser();
function logoutUser()
{
    session_destroy();
    header("Location: login.php");
    exit;
}
