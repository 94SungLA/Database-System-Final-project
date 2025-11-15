<?php
$dsn = "mysql:host=localhost;dbname=seavice;charset=utf8mb4"; // 資料庫名稱為 seavice
$user = "root"; // XAMPP 預設使用者
$pass = ""; // XAMPP 預設空密碼

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    exit("資料庫連線失敗：" . $e->getMessage());
}