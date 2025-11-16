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
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: #333;
        }

        form {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #5a6fd8;
        }

        p {
            margin-top: 20px;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <form method="post">
        <label for="name">姓名:</label>
        <input id="name" name="name" type="text" placeholder="姓名" required>
        <label for="email">Email:</label>
        <input id="email" name="email" type="email" placeholder="Email" required>
        <label for="password">密碼:</label>
        <input id="password" name="password" type="password" placeholder="密碼" required>
        <label for="confirm_password">確認密碼:</label>
        <input id="confirm_password" name="confirm_password" type="password" placeholder="確認密碼" required>
        <label for="phone">電話:</label>
        <input id="phone" name="phone" type="text" placeholder="電話" required>
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <button>註冊</button>
        <?php if (!empty($error)) echo "<p style='color:red'>$error</p>"; ?>
        <p>已有帳號？<a href="login.php" style="color: #667eea; text-decoration: none;">登入</a></p>
    </form>
</body>

</html>