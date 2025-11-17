<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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

// 新增：更新現有評價
// usage: updateReview($review_id, $rating, $comment)
function updateReview($review_id, $rating, $comment)
{
    global $pdo;
    $stmt = $pdo->prepare("UPDATE Reviews SET rating = ?, comment = ? WHERE review_id = ?");
    $result = $stmt->execute([$rating, $comment, $review_id]);
    if ($result) {
        // 重新計算評價
        $stmt = $pdo->prepare("SELECT reviewee_id FROM Reviews WHERE review_id = ?");
        $stmt->execute([$review_id]);
        $reviewee_id = $stmt->fetchColumn();
        recalculateUserRating($reviewee_id);
    }
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

// 新增函數：獲取使用者的評分和評價次數
// usage: getUserRating($user_id, $role) where $role is 'requester' or 'runner'
function getUserRating($user_id, $role)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT rating_as_$role, review_count_$role FROM Users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// 新增函數：獲取使用者的評論列表
// usage: getUserReviews($user_id, $role, $limit) where $role is 'requester' or 'runner'
function getUserReviews($user_id, $role, $limit = 5)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT r.rating, r.comment, r.created_at, u.name AS reviewer_name
                           FROM Reviews r
                           JOIN Users u ON r.reviewer_id = u.user_id
                           WHERE r.reviewee_id = ? AND r.role = ?
                           ORDER BY r.created_at DESC
                           LIMIT ?");
    $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
    $stmt->bindValue(2, $role, PDO::PARAM_STR);
    $stmt->bindValue(3, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    $review_id = $_POST['review_id'] ?? null;

    if (!$task_id || !$reviewer_id || !$reviewee_id || !$role || !$rating || $rating > 5 || !$comment) {
        echo json_encode(['success' => false, 'message' => '無效參數：請確保所有欄位均已填寫']);
        exit;
    }

    try {
        if ($review_id) {
            // 編輯現有評價
            $result = updateReview($review_id, $rating, $comment);
            $message = $result ? '評價更新成功' : '評價更新失敗：未知錯誤';
        } else {
            // 創建新評價
            $result = createReview($task_id, $reviewer_id, $reviewee_id, $role, $rating, $comment);
            $message = $result ? '評價提交成功' : '評價提交失敗：未知錯誤';
        }
        echo json_encode(['success' => $result, 'message' => $message]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => '操作失敗：' . $e->getMessage()]);
    }
    exit;
}
