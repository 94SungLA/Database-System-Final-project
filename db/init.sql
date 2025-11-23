-- Database create
DROP DATABASE IF EXISTS seavice;
CREATE DATABASE seavice DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE seavice;
-- -----------------------------
--  Users Table
-- -----------------------------
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
-- default users
INSERT INTO Users (
        name,
        email,
        password_hash,
        phone,
        is_admin,
        is_banned,
        created_at
    )
VALUES (
        '小美',
        'mei@example.com',
        '$2a$10$8uVwqZ8sHcL4eD3lXjG1qO9wT0uABF4MJabc123xyz',
        '0911222333',
        FALSE,
        FALSE,
        CURRENT_TIMESTAMP
    ),
    (
        '阿強',
        'qiang@example.com',
        '$2a$10$ycZ2C9e8HoQvF.7YJKsZ2C3dNqC.2LxG8f9Qwe56tU',
        '0922333444',
        FALSE,
        TRUE,
        CURRENT_TIMESTAMP
    ),
    (
        '老王',
        'admin@example.com',
        '$2a$10$qWx8zV9sHcL4eD3lKjF1pN8qZ0aB4CD7EfGhIjKlMn',
        '0933444555',
        TRUE,
        FALSE,
        CURRENT_TIMESTAMP
    ),
    (
        'CS',
        'CS@example.com',
        '$2a$10$qWx8zV9sHcL4eD3lKjF1pN8qZ0aB4CD7EfGhIjKlMn',
        '09988165782',
        FALSE,
        TRUE,
        CURRENT_TIMESTAMP
    ),
    (
        '阿喜',
        'si@email.com',
        '$2a$10$ycZ2C9e8HoQvF.7YJKsZ2C3dNqC.2LxG8f9Qwe56tU',
        '095544179123',
        FALSE,
        FALSE,
        CURRENT_TIMESTAMP
    );
-- -----------------------------
--  Tasks Table
-- -----------------------------
CREATE TABLE Tasks (
    task_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    tags ENUM ('跑腿', '代購', '送件', '其他') DEFAULT NULL,
    reward INT,
    status ENUM (
        'open',
        'confirming',
        'in_progress',
        'completed',
        'cancelled'
    ) DEFAULT 'open',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    deadline DATETIME,
    location_tags VARCHAR(255) DEFAULT NULL,
    -- 新增欄位以儲存地點標籤
    completed_at DATETIME DEFAULT NULL,
    -- 新增欄位以儲存任務完成時間
    requester_id INT NOT NULL,
    runner_id INT,
    FOREIGN KEY (requester_id) REFERENCES Users (user_id),
    FOREIGN KEY (runner_id) REFERENCES Users (user_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- insert 的評價不能非正整數
DELIMITER //
CREATE TRIGGER trg_tasks_check_reward_bi
BEFORE INSERT ON Tasks
FOR EACH ROW
BEGIN
    IF NEW.reward <= 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'reward must be greater than 0';
    END IF;
END//
DELIMITER ;

-- update 的評價不能非正整數
DELIMITER //
CREATE TRIGGER trg_tasks_check_reward_bu
BEFORE UPDATE ON Tasks
FOR EACH ROW
BEGIN
    IF NEW.reward <= 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'reward must be greater than 0';
    END IF;
END//
DELIMITER ;

INSERT INTO Tasks (
        title,
        description,
        tags,
        reward,
        status,
        requester_id,
        runner_id,
        deadline,
        location_tags,
        completed_at
    )
VALUES (
        '幫我買奶茶',
        '從校門口飲料店買一杯無糖珍奶',
        '跑腿',
        60,
        'completed',
        1,
        2,
        '2025-11-09 18:00',
        '基隆, 校門口, 飲料店',
        '2025-11-17 17:00'
    ),
    (
        '幫我寄信到郵局',
        '請於下午三點前送達',
        '送件',
        40,
        'in_progress',
        2,
        4,
        '2025-11-10 15:00',
        '基隆, 郵局',
        NULL
    ),
    (
        '幫我拿包裹',
        '超商取件到宿舍',
        '送件',
        50,
        'confirming',
        1,
        2,
        '2025-11-09 19:00',
        '宿舍, 超商',
        NULL
    );
INSERT INTO Tasks (
        title,
        description,
        tags,
        reward,
        status,
        deadline,
        location_tags,
        requester_id
    )
VALUES (
        '幫忙去 7-11 取包裹',
        '在海大附近 7-11 取已付款包裹，A4 大小',
        '跑腿',
        90,
        'in_progress',
        '2025-11-18 20:00',
        '基隆,七堵,7-11',
        2
    ),
    (
        '代購麥當勞宵夜',
        '大麥克餐 + 冰奶茶，送到宿舍',
        '代購',
        110,
        'open',
        '2025-11-18 23:00',
        '基隆,海大,北寧宿舍',
        3
    ),
    (
        '文件送到行政大樓體育室',
        '把系羽申請表送到行政大樓 5 樓',
        '送件',
        60,
        'open',
        '2025-11-17 12:00',
        '海大,行政大樓',
        1
    ),
    (
        '搬宿舍小幫手',
        '從北寧搬到祥豐四箱生活用品，附飲料',
        '其他',
        200,
        'open',
        '2025-11-19 17:00',
        '海大,北寧宿舍,祥豐宿舍',
        4
    ),
    (
        '幫修腳踏車鏈條',
        '鏈條怪怪的會爆音，可補材料費',
        '其他',
        350,
        'open',
        '2025-11-25 19:00',
        '基隆,腳踏車店',
        2
    ),
    (
        '代寄包裹到全家',
        '面單已列印，只需代寄即可',
        '跑腿',
        80,
        'open',
        '2025-11-20 16:00',
        '基隆,仁二路,全家',
        3
    ),
    (
        '影印講義 50 份雙面黑白',
        '送到資工系館 307',
        '跑腿',
        150,
        'open',
        '2025-11-21 14:00',
        '海大,資工大樓',
        5
    ),
    (
        '代購星巴克',
        '兩杯 Venti 拿鐵無糖去冰送到和平校區',
        '代購',
        180,
        'open',
        '2025-11-18 15:30',
        '信義區,星巴克,和平校區',
        1
    ),
    (
        '幫遛狗 30 分鐘',
        '柴柴乖且不會亂叫，附飲料',
        '其他',
        120,
        'open',
        '2025-11-18 18:30',
        '暖暖,公園',
        4
    ),
    (
        '送急件契約書到南港',
        '急件！從中山送到南港',
        '送件',
        300,
        'open',
        '2025-11-18 14:00',
        '中山區,南港',
        4
    );
-- -----------------------------
--  Reviews Table
-- -----------------------------
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

-- insert, update 用到的 check
DELIMITER //
CREATE PROCEDURE check_rating(IN r DECIMAL(2,1))
BEGIN
    IF r < 0 OR r > 5 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'rating must be between 0 and 5';
    END IF;
END//
DELIMITER ;

-- 不可insert out-of-range的評價
DELIMITER //
CREATE TRIGGER trg_reviews_check_rating_bi
BEFORE INSERT ON Reviews
FOR EACH ROW
BEGIN
    CALL check_rating(NEW.rating);
END//
DELIMITER ;

-- 不可update out-of-range的評價
DELIMITER //
CREATE TRIGGER trg_reviews_check_rating_bu
BEFORE UPDATE ON Reviews
FOR EACH ROW
BEGIN
    CALL check_rating(NEW.rating);
END//
DELIMITER ;

DELIMITER //

-- AFTER INSERT Trigger
CREATE TRIGGER trg_reviews_after_insert
AFTER INSERT ON Reviews
FOR EACH ROW
BEGIN
    -- 更新 runner 評分和數量
    IF NEW.role = 'runner' THEN
        UPDATE Users u
        SET rating_as_runner = (
                SELECT AVG(r.rating)
                FROM Reviews r
                WHERE r.reviewee_id = NEW.reviewee_id
                  AND r.role = 'runner'
            ),
            review_count_runner = (
                SELECT COUNT(*)
                FROM Reviews r
                WHERE r.reviewee_id = NEW.reviewee_id
                  AND r.role = 'runner'
            )
        WHERE u.user_id = NEW.reviewee_id;
    END IF;

    -- 更新 requester 評分和數量
    IF NEW.role = 'requester' THEN
        UPDATE Users u
        SET rating_as_requester = (
                SELECT AVG(r.rating)
                FROM Reviews r
                WHERE r.reviewee_id = NEW.reviewee_id
                  AND r.role = 'requester'
            ),
            review_count_requester = (
                SELECT COUNT(*)
                FROM Reviews r
                WHERE r.reviewee_id = NEW.reviewee_id
                  AND r.role = 'requester'
            )
        WHERE u.user_id = NEW.reviewee_id;
    END IF;
END;
//

-- AFTER UPDATE Trigger
CREATE TRIGGER trg_reviews_after_update
AFTER UPDATE ON Reviews
FOR EACH ROW
BEGIN
    -- Runner
    IF NEW.role = 'runner' THEN
        UPDATE Users u
        SET rating_as_runner = (
                SELECT AVG(r.rating)
                FROM Reviews r
                WHERE r.reviewee_id = NEW.reviewee_id
                  AND r.role = 'runner'
            ),
            review_count_runner = (
                SELECT COUNT(*)
                FROM Reviews r
                WHERE r.reviewee_id = NEW.reviewee_id
                  AND r.role = 'runner'
            )
        WHERE u.user_id = NEW.reviewee_id;
    END IF;

    -- Requester
    IF NEW.role = 'requester' THEN
        UPDATE Users u
        SET rating_as_requester = (
                SELECT AVG(r.rating)
                FROM Reviews r
                WHERE r.reviewee_id = NEW.reviewee_id
                  AND r.role = 'requester'
            ),
            review_count_requester = (
                SELECT COUNT(*)
                FROM Reviews r
                WHERE r.reviewee_id = NEW.reviewee_id
                  AND r.role = 'requester'
            )
        WHERE u.user_id = NEW.reviewee_id;
    END IF;
END;
//

DELIMITER ;

-- sample reviews
INSERT INTO Reviews (
        task_id,
        reviewer_id,
        reviewee_id,
        role,
        rating,
        comment
    )
VALUES (1, 1, 2, 'runner', 5.0, '準時又親切！'),
    (1, 2, 1, 'requester', 4.5, '描述清楚，好合作');