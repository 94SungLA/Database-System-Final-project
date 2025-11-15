<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../src/Database.php';

$db = new Database();
$conn = $db->getConnection();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>首頁</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <h1>專案首頁</h1>
    <p>PHP + MariaDB 開發環境已啟動！</p>
</body>

</html>