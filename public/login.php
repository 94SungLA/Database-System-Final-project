<?php
require_once "../backend/user.php";
require_once "../backend/auth.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user = findUserByEmail($_POST["email"]);
    if ($user && password_verify($_POST["password"], $user["password_hash"])) {
        $_SESSION["user"] = $user;
        header("Location: index.php");
        exit;
    }
    $error = "帳號或密碼錯誤";
}
?>
<form method="post">
    <input name="email" placeholder="Email">
    <input name="password" type="password" placeholder="Password">
    <button>登入</button>
    <?php if (isset($error))
        echo "<p style='color:red'>$error</p>"; ?>
</form>