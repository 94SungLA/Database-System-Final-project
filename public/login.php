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
        <label for="email">Email:</label>
        <input id="email" name="email" type="email" placeholder="Email" required>
        <label for="password">密碼:</label>
        <input id="password" name="password" type="password" placeholder="Password" required>
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <button type="submit">登入</button>
        <?php if (!empty($error)) echo "<p style='color:red'>$error</p>"; ?>
        <p>沒有帳號？<a href="register.php" style="color: #667eea; text-decoration: none;">註冊</a></p>
    </form>
</body>

</html>