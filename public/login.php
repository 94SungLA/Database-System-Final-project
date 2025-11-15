<?php
session_start();
require_once "../backend/auth.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (loginUser($_POST["email"], $_POST["password"])) {
        header("Location: index.php");
        exit;
    }
    $error = "帳號或密碼錯誤";
}
?>
<form method="post">
    <input name="email" placeholder="Email" required>
    <input name="password" type="password" placeholder="Password" required>
    <button>登入</button>
    <?php if (isset($error))
        echo "<p style='color:red'>$error</p>"; ?>
</form>