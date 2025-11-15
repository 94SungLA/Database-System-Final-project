<?php
require_once "../backend/user.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    createUser($_POST["name"], $_POST["email"], $_POST["password"], $_POST["phone"]);
    header("Location: login.php");
    exit;
}
?>
<form method="post">
    <input name="name" placeholder="姓名">
    <input name="email" placeholder="Email">
    <input name="password" type="password" placeholder="密碼">
    <input name="phone" placeholder="電話">
    <button>註冊</button>
</form>