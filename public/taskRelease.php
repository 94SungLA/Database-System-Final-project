<?php
require_once "../backend/auth.php";
requireLogin();
// var_dump($_SESSION);
require_once "../backend/task.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    createTask($_POST["title"], $_POST["desc"], $_POST["tags"], $_POST["reward"], $_POST["deadline"], $_SESSION["user"]["user_id"]);
    header("Location: index.php");
    exit;
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