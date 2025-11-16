<?php
require_once "../backend/auth.php";
requireLogin();
// var_dump($_SESSION);
require_once "../backend/task.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 驗證輸入
    $title = trim($_POST["title"]);
    $desc = trim($_POST["desc"]);
    $tags = $_POST["tags"];
    $reward = (int)$_POST["reward"];
    $deadline = $_POST["deadline"];

    if (empty($title) || strlen($title) > 100) {
        $error = "標題必填且不超過100字元";
    } elseif ($reward <= 0) {
        $error = "酬勞必須為正整數";
    } elseif (strtotime($deadline) <= time()) {
        $error = "截止時間必須為未來";
    } else {
        createTask($title, $desc, $tags, $reward, $deadline, $_SESSION["user"]["user_id"]);
        header("Location: index.php");
        exit;
    }
}
?>
<form method="post">
    <input name="title" placeholder="標題">
    <textarea name="desc" placeholder="內容"></textarea>
    <select name="tags">
        <option value="跑腿">跑腿</option>
        <option value="代購">代購</option>
        <option value="送件">送件</option>
    </select>
    <input name="reward" type="number" placeholder="酬勞">
    <input name="deadline" type="datetime-local">
    <button>發布任務</button>
</form>