<?php
session_start();
require_once "../backend/user.php";

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
        $name = filter_var($_POST["name"], FILTER_SANITIZE_STRING);
        $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
        $password = $_POST["password"];
        $confirm_password = $_POST["confirm_password"];
        $phone = filter_var($_POST["phone"], FILTER_SANITIZE_STRING);

        // 基本驗證
        if (empty($name) || empty($email) || empty($password) || empty($phone)) {
            $error = "所有欄位皆為必填";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "請輸入有效 email";
        } elseif (strlen($password) < 6) {
            $error = "密碼至少 6 個字元";
        } elseif ($password !== $confirm_password) {
            $error = "密碼確認不匹配";
        } else {
            if (createUser($name, $email, $password, $phone)) {
                header("Location: login.php");
                exit;
            } else {
                $error = "註冊失敗，請重試";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <title>註冊</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="font-sans bg-gradient-to-br from-blue-500 to-purple-600 flex justify-center items-center h-screen m-0 text-gray-800">
    <form method="post" class="bg-white p-10 rounded-lg shadow-lg w-full max-w-md text-center">
        <label for="name" class="block mb-2 font-bold text-gray-700">姓名:</label>
        <input id="name" name="name" type="text" placeholder="姓名" required class="w-full p-3 mb-5 border border-gray-300 rounded-md box-border text-base">
        <label for="email" class="block mb-2 font-bold text-gray-700">Email:</label>
        <input id="email" name="email" type="email" placeholder="Email" required class="w-full p-3 mb-5 border border-gray-300 rounded-md box-border text-base">
        <label for="password" class="block mb-2 font-bold text-gray-700">密碼:</label>
        <input id="password" name="password" type="password" placeholder="密碼" required class="w-full p-3 mb-5 border border-gray-300 rounded-md box-border text-base">
        <label for="confirm_password" class="block mb-2 font-bold text-gray-700">確認密碼:</label>
        <input id="confirm_password" name="confirm_password" type="password" placeholder="確認密碼" required class="w-full p-3 mb-5 border border-gray-300 rounded-md box-border text-base">
        <label for="phone" class="block mb-2 font-bold text-gray-700">電話:</label>
        <input id="phone" name="phone" type="text" placeholder="電話" required class="w-full p-3 mb-5 border border-gray-300 rounded-md box-border text-base">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <button class="w-full p-3 bg-blue-500 text-white border-none rounded-md text-base cursor-pointer transition-colors hover:bg-blue-600">註冊</button>
        <?php if (!empty($error)) echo "<p class='mt-5 text-sm text-red-500'>$error</p>"; ?>
        <p class="mt-5 text-sm">已有帳號？<a href="login.php" class="text-blue-500 no-underline">登入</a></p>
    </form>
</body>

</html>