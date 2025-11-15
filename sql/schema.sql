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
    is_admin BOOLEAN DEFAULT FALSE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
CREATE TABLE Tasks (
    task_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    tags ENUM('跑腿', '代購', '送件', '其他') DEFAULT '其他',
    reward INT CHECK (reward > 0),
    status ENUM(
        'open',
        'confirming',
        'in_progress',
        'completed',
        'cancelled'
    ) DEFAULT 'open',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    deadline DATETIME,
    requester_id INT NOT NULL,
    runner_id INT,
    FOREIGN KEY (requester_id) REFERENCES Users(user_id),
    FOREIGN KEY (runner_id) REFERENCES Users(user_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
CREATE TABLE Reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT NOT NULL,
    reviewer_id INT NOT NULL,
    reviewee_id INT NOT NULL,
    role ENUM('requester', 'runner') NOT NULL,
    rating DECIMAL(2, 1) CHECK (
        rating BETWEEN 1 AND 5
    ),
    comment TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES Tasks(task_id),
    FOREIGN KEY (reviewer_id) REFERENCES Users(user_id),
    FOREIGN KEY (reviewee_id) REFERENCES Users(user_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;