# Database Schema & Operations Documentation

此文件統整了專案中所有的 DDL (Data Definition Language)、DML (Data Manipulation Language)、Triggers、Functions 以及 Procedures。

## 1. DDL (Data Definition Language)

資料庫結構定義，主要位於 `db/init.sql`。

### Database
```sql
CREATE DATABASE seavice DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE seavice;
```

### Tables

#### Users Table
使用者資料表，儲存使用者基本資訊、評分與權限狀態。
```sql
CREATE TABLE Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    rating_as_requester DECIMAL(2, 1) DEFAULT NULL,
    rating_as_runner DECIMAL(2, 1) DEFAULT NULL,
    review_count_requester INT DEFAULT 0,
    review_count_runner INT DEFAULT 0,
    is_admin BOOLEAN DEFAULT FALSE,
    is_banned BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
```

#### Tasks Table
任務資料表，儲存任務詳情、狀態、地點與關聯的使用者。
```sql
CREATE TABLE Tasks (
    task_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    tags ENUM ('跑腿', '代購', '送件', '其他') DEFAULT NULL,
    reward INT,
    status ENUM ('open', 'confirming', 'in_progress', 'completed', 'cancelled') DEFAULT 'open',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    deadline DATETIME,
    location_tags VARCHAR(255) DEFAULT NULL,
    completed_at DATETIME DEFAULT NULL,
    requester_id INT NOT NULL,
    runner_id INT,
    FOREIGN KEY (requester_id) REFERENCES Users (user_id),
    FOREIGN KEY (runner_id) REFERENCES Users (user_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
```

#### Reviews Table
評價資料表，儲存任務完成後的互評資訊。
```sql
CREATE TABLE Reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT NOT NULL,
    reviewer_id INT NOT NULL,
    reviewee_id INT NOT NULL,
    role ENUM ('requester', 'runner') NOT NULL,
    rating DECIMAL(2, 1) NOT NULL,
    comment TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES Tasks (task_id),
    FOREIGN KEY (reviewer_id) REFERENCES Users (user_id),
    FOREIGN KEY (reviewee_id) REFERENCES Users (user_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
```

## 2. Procedures

位於 `db/init.sql`。

### check_rating
檢查評分是否在 0 到 5 之間，若否則拋出錯誤。
```sql
CREATE PROCEDURE check_rating(IN r DECIMAL(2,1))
BEGIN
    IF r < 0 OR r > 5 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'rating must be between 0 and 5';
    END IF;
END
```

## 3. Triggers

位於 `db/init.sql`，用於資料驗證與自動更新。

### Tasks 相關 Trigger
驗證 reward 必須大於 0。

**trg_tasks_check_reward_bi (Before Insert)**
```sql
CREATE TRIGGER trg_tasks_check_reward_bi
BEFORE INSERT ON Tasks
FOR EACH ROW
BEGIN
    IF NEW.reward <= 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'reward must be greater than 0';
    END IF;
END
```

**trg_tasks_check_reward_bu (Before Update)**
```sql
CREATE TRIGGER trg_tasks_check_reward_bu
BEFORE UPDATE ON Tasks
FOR EACH ROW
BEGIN
    IF NEW.reward <= 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'reward must be greater than 0';
    END IF;
END
```

### Reviews 相關 Trigger
驗證 rating 範圍並自動更新 User 評分。

**trg_reviews_check_rating_bi (Before Insert)**
呼叫 `check_rating` procedure 驗證分數。
```sql
CREATE TRIGGER trg_reviews_check_rating_bi
BEFORE INSERT ON Reviews
FOR EACH ROW
BEGIN
    CALL check_rating(NEW.rating);
END
```

**trg_reviews_check_rating_bu (Before Update)**
呼叫 `check_rating` procedure 驗證分數。
```sql
CREATE TRIGGER trg_reviews_check_rating_bu
BEFORE UPDATE ON Reviews
FOR EACH ROW
BEGIN
    CALL check_rating(NEW.rating);
END
```

**trg_reviews_after_insert (After Insert)**
**[AGGREGATE]** 新增評價後，自動計算並更新使用者的平均評分與評價數量。
```sql
CREATE TRIGGER trg_reviews_after_insert
AFTER INSERT ON Reviews
FOR EACH ROW
BEGIN
    -- 更新 runner 評分和數量
    IF NEW.role = 'runner' THEN
        UPDATE Users u
        SET rating_as_runner = (SELECT AVG(r.rating) FROM Reviews r WHERE r.reviewee_id = NEW.reviewee_id AND r.role = 'runner'),
            review_count_runner = (SELECT COUNT(*) FROM Reviews r WHERE r.reviewee_id = NEW.reviewee_id AND r.role = 'runner')
        WHERE u.user_id = NEW.reviewee_id;
    END IF;

    -- 更新 requester 評分和數量
    IF NEW.role = 'requester' THEN
        UPDATE Users u
        SET rating_as_requester = (SELECT AVG(r.rating) FROM Reviews r WHERE r.reviewee_id = NEW.reviewee_id AND r.role = 'requester'),
            review_count_requester = (SELECT COUNT(*) FROM Reviews r WHERE r.reviewee_id = NEW.reviewee_id AND r.role = 'requester')
        WHERE u.user_id = NEW.reviewee_id;
    END IF;
END
```

**trg_reviews_after_update (After Update)**
**[AGGREGATE]** 更新評價後，重新計算並更新使用者的平均評分與評價數量。
```sql
CREATE TRIGGER trg_reviews_after_update
AFTER UPDATE ON Reviews
FOR EACH ROW
BEGIN
    -- (邏輯同 trg_reviews_after_insert)
    -- ...
END
```

## 4. DML (Data Manipulation Language)

分散在 `backend/*.php` 中，用於應用程式邏輯。

### Users 相關
(`backend/user.php`, `backend/admin.php`)

*   **SELECT**
    *   登入/檢查 Email: `SELECT * FROM Users WHERE email=?`
    *   取得使用者資料: `SELECT * FROM Users WHERE user_id=?`
    *   更新資料時檢查 Email 重複: `SELECT user_id FROM Users WHERE email = ? AND user_id != ?`
    *   **[AGGREGATE]** (Admin) 統計總用戶數: `SELECT count(user_id) as total from users`
    *   **[MULTI-JOIN] [AGGREGATE]** (Admin) 取得所有使用者列表: `SELECT u.*, ... FROM Users u ...`
*   **INSERT**
    *   註冊: `INSERT INTO Users (name, email, password_hash, phone, created_at) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)`
*   **UPDATE**
    *   更新個人資料: `UPDATE Users SET name = ?, phone = ?, email = ? WHERE user_id = ?`
    *   (Admin) 停權/復權: `UPDATE Users as u SET u.is_banned = ? WHERE u.user_id=?`

### Tasks 相關
(`backend/task.php`, `backend/admin.php`)

*   **SELECT**
    *   **[JOIN]** 取得公開任務列表: `SELECT t.*, u.name AS requester_name, u.rating_as_requester FROM Tasks t JOIN Users u ... WHERE status='open' ...`
    *   **[MULTI-JOIN]** 取得單一任務詳情: `SELECT t.*, ... FROM Tasks as t ... WHERE t.task_id=?`
    *   **[MULTI-JOIN]** 取得我發布的任務: `SELECT t.*, ... FROM Tasks t ... WHERE t.requester_id = ? ...`
    *   **[MULTI-JOIN]** 取得我接取的任務: `SELECT t.*, ... FROM Tasks t ... WHERE t.runner_id = ? ...`
    *   **[AGGREGATE]** (Admin) 統計進行中任務: `SELECT count(task_id) as total from tasks where status='in_progress'`
    *   **[AGGREGATE]** (Admin) 統計今日完成任務: `SELECT count(task_id) as total from tasks where status='completed' AND DATE(completed_at)=CURDATE()`
    *   **[AGGREGATE]** (Admin) 統計任務分類: `SELECT t.tags AS tag, COUNT(t.task_id) AS num ... FROM Tasks t GROUP BY t.tags`
    *   **[MULTI-JOIN]** (Admin) 取得所有任務: `SELECT t.*, ... FROM Tasks t ...`
*   **INSERT**
    *   發布任務: `INSERT INTO Tasks (title, description, tags, reward, deadline, location_tags, requester_id) VALUES (?, ?, ?, ?, ?, ?, ?)`
*   **UPDATE**
    *   申請任務 (Runner): `UPDATE Tasks SET runner_id = ?, status = 'confirming' WHERE task_id = ? AND status = 'open'`
    *   批准任務 (Requester): `UPDATE Tasks SET status = 'in_progress' WHERE task_id = ? AND runner_id = ? AND status = 'confirming'`
    *   拒絕任務 (Requester): `UPDATE Tasks SET runner_id = NULL, status = 'open' WHERE task_id = ? AND requester_id = ? AND status = 'confirming'`
    *   完成任務 (Runner): `UPDATE Tasks SET status = 'completed', completed_at = NOW() WHERE task_id = ? AND runner_id = ?`
    *   取消任務 (Requester): `UPDATE Tasks SET status = 'cancelled' WHERE task_id = ? AND requester_id = ? ...`
*   **DELETE**
    *   (Admin) 刪除任務: `DELETE FROM Tasks WHERE task_id = ?`

### Reviews 相關
(`backend/review.php`, `backend/admin.php`)

*   **SELECT**
    *   取得被評論者 ID: `SELECT reviewee_id FROM Reviews WHERE review_id = ?`
    *   **[JOIN]** 取得使用者評論列表: `SELECT r.rating, r.comment, ... FROM Reviews r ... WHERE r.reviewee_id = ? ...`
*   **INSERT**
    *   新增評論: `INSERT INTO Reviews (task_id, reviewer_id, reviewee_id, role, rating, comment) VALUES (?, ?, ?, ?, ?, ?)`
*   **UPDATE**
    *   更新評論: `UPDATE Reviews SET rating = ?, comment = ? WHERE review_id = ?`
    *   **[AGGREGATE]** 手動重新計算評分 (備用邏輯): `UPDATE Users u SET rating_as_runner = ..., review_count_runner = ... WHERE u.user_id = ?`
*   **DELETE**
    *   刪除任務時連帶刪除評論: `DELETE FROM Reviews WHERE task_id = ?`
    *   (Admin) 刪除評論: `DELETE FROM Reviews WHERE review_id=? AND role=?`
