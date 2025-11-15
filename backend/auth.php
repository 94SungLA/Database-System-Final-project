<?php
session_start();

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