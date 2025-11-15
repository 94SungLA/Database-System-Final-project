# Database-System-Final-project

# seavice Database Initialization
匯入 init.sql 後會自動建立：
- 3 個使用者（含管理員）
- 3 個任務狀態（open / confirming / completed）
- 雙方互評範例資料

# 專案目錄結構
Database-System-Final-project/
│── db/
│   └── init.sql          ← 建表 + 初始假資料
│
│── backend/
│   ├── db.php            ← PDO 連線
│   ├── auth.php          ← session 登入驗證
│   ├── user.php          ← 使用者 CRUD
│   ├── task.php          ← 任務 CRUD
│
│── public/
│   ├── login.php
│   ├── logout.php
│   ├── register.php
│   ├── index.php         ← 任務列表
│   ├── taskRelease.php   ← 發布任務
│   ├── viewTask.php      ← 任務詳情
│   ├── myTask.php        ← 我接的/我發的任務
│   └── admin/
│       ├── userManage.php
│       └── taskManage.php
│
│── assets/
│   ├── style.css         ← 可有可無
│   └── logo.png          ← 之後要放 icon 用
│
└── README.md             ← 專案說明、環境設定、成員資訊