<?php
session_start();
require_once "db.php";


// 建立新評價
// usage: createReview($task_id, $reviewer_id, $reviewee_id,
function createReview($task_id, $reviewer_id, $reviewee_id, $role, $rating, $comment)
{
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO Reviews (task_id, reviewer_id, reviewee_id, role, rating, comment)
                           VALUES (?, ?, ?, ?, ?, ?)");
    $result = $stmt->execute([$task_id, $reviewer_id, $reviewee_id, $role, $rating, $comment]);
    recalculateUserRating($reviewee_id);
    return $result;
}

// 重新計算並更新使用者的評價與評價次數
// usage: recalculateUserRating($user_id);
function recalculateUserRating($user_id)
{
    global $pdo;

    $stmt = $pdo->prepare("UPDATE Users u
        SET
        rating_as_runner = (
            SELECT AVG(r.rating)
            FROM Reviews r
            WHERE r.reviewee_id = u.user_id AND r.role = 'runner'
        ),
        review_count_runner = (
            SELECT COUNT(*)
            FROM Reviews r
            WHERE r.reviewee_id = u.user_id AND r.role = 'runner'
        ),
        rating_as_requester = (
            SELECT AVG(r.rating)
            FROM Reviews r
            WHERE r.reviewee_id = u.user_id AND r.role = 'requester'
        ),
        review_count_requester = (
            SELECT COUNT(*)
            FROM Reviews r
            WHERE r.reviewee_id = u.user_id AND r.role = 'requester'
        )
    WHERE u.user_id = ?");

    $stmt->execute([$user_id]);
}

// Handle POST request for creating review
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $task_id = $_POST['task_id'] ?? null;
    $reviewer_id = $_SESSION['user']['user_id'] ?? null; // Assume session is set
    $reviewee_id = $_POST['reviewee_id'] ?? null;
    $role = $_POST['role'] ?? null;
    $rating = $_POST['rating'] ?? null;
    $comment = $_POST['comment'] ?? null;

    if (!$task_id || !$reviewer_id || !$reviewee_id || !$role || !$rating || $rating < 1 || $rating > 5 || !$comment) {
        echo json_encode(['success' => false, 'message' => '無效參數：請確保所有欄位均已填寫']);
        exit;
    }

    $result = createReview($task_id, $reviewer_id, $reviewee_id, $role, $rating, $comment);
    if ($result) {
        echo json_encode(['success' => true, 'message' => '評價提交成功']);
    } else {
        echo json_encode(['success' => false, 'message' => '評價提交失敗']);
    }
    exit;
}
