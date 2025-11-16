<?php
session_start();
require_once "../backend/auth.php";

// 產生 CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 驗證 CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "無效請求";
    } else {
        // 消毒輸入
        $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
        $password = $_POST["password"];

        // 基本驗證
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "請輸入有效 email";
        } elseif (strlen($password) < 6) {
            $error = "密碼至少 6 個字元";
        } else {
            if (loginUser($email, $password)) {
                header("Location: index.php");
                exit;
            }
            $error = "帳號或密碼錯誤";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <title>登入</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="font-sans bg-gradient-to-br from-blue-500 to-purple-600 flex justify-center items-center h-screen m-0 text-gray-800">
    <form method="post" class="bg-white p-10 rounded-lg shadow-lg w-full max-w-md text-center">
        <label for="email" class="block mb-2 font-bold text-gray-700">Email:</label>
        <input id="email" name="email" type="email" placeholder="Email" required class="w-full p-3 mb-5 border border-gray-300 rounded-md box-border text-base">
        <label for="password" class="block mb-2 font-bold text-gray-700">密碼:</label>
        <input id="password" name="password" type="password" placeholder="Password" required class="w-full p-3 mb-5 border border-gray-300 rounded-md box-border text-base">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <button type="submit" class="w-full p-3 bg-blue-500 text-white border-none rounded-md text-base cursor-pointer transition-colors hover:bg-blue-600">登入</button>
        <?php if (!empty($error)) echo "<p class='mt-5 text-sm text-red-500'>$error</p>"; ?>
        <p class="mt-5 text-sm">沒有帳號？<a href="register.php" class="text-blue-500 no-underline">註冊</a></p>
    </form>
</body>

</html>