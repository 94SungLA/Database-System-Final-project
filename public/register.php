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
            $result = createUser($name, $email, $password, $phone);
            if ($result === "Email already in use") {
                $error = "此 Email 已被使用";
            } elseif ($result === true) {
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #f5576c 75%, #4facfe 100%);
            background-size: 400% 400%;
            animation: gradientShift 10s ease infinite;
        }

        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .form-container {
            animation: fadeIn 0.8s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .btn-hover:hover {
            transform: scale(1.05);
            transition: transform 0.2s ease;
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        .input-icon input {
            padding-left: 40px;
        }
    </style>
</head>

<body class="font-sans flex justify-center items-center h-screen m-0 text-gray-800">
    <form method="post" class="form-container bg-white p-10 rounded-lg shadow-xl w-full max-w-md text-center"
        style="box-shadow: 0 10px 25px rgba(0,0,0,0.1), 0 5px 10px rgba(0,0,0,0.05);">
        <label for="name" class="block mb-2 font-bold text-gray-700">姓名:</label>
        <div class="input-icon mb-5">
            <i class="fas fa-user"></i>
            <input id="name" name="name" type="text" placeholder="姓名" required
                class="w-full p-3 border border-gray-300 rounded-md box-border text-base focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <label for="email" class="block mb-2 font-bold text-gray-700">Email:</label>
        <div class="input-icon mb-5">
            <i class="fas fa-envelope"></i>
            <input id="email" name="email" type="email" placeholder="Email" required
                class="w-full p-3 border border-gray-300 rounded-md box-border text-base focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <label for="password" class="block mb-2 font-bold text-gray-700">密碼:</label>
        <div class="input-icon mb-5">
            <i class="fas fa-lock"></i>
            <input id="password" name="password" type="password" placeholder="密碼" required
                class="w-full p-3 border border-gray-300 rounded-md box-border text-base focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <label for="confirm_password" class="block mb-2 font-bold text-gray-700">確認密碼:</label>
        <div class="input-icon mb-5">
            <i class="fas fa-lock"></i>
            <input id="confirm_password" name="confirm_password" type="password" placeholder="確認密碼" required
                class="w-full p-3 border border-gray-300 rounded-md box-border text-base focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <label for="phone" class="block mb-2 font-bold text-gray-700">電話:</label>
        <div class="input-icon mb-5">
            <i class="fas fa-phone"></i>
            <input id="phone" name="phone" type="text" placeholder="電話" required
                class="w-full p-3 border border-gray-300 rounded-md box-border text-base focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <button
            class="btn-hover w-full p-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white border-none rounded-md text-base cursor-pointer transition-all hover:from-blue-600 hover:to-blue-700">
            註冊
        </button>
        <?php if (!empty($error))
            echo "<p class='mt-5 text-sm text-red-500'>$error</p>"; ?>
        <p class="mt-5 text-sm">已有帳號？<a href="login.php" class="text-blue-500 no-underline hover:underline">登入</a></p>
    </form>
</body>

</html>