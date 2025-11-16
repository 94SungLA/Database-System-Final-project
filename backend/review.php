<?php
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
