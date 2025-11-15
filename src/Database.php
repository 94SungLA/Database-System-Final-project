<?php
class Database
{
    private $conn;

    public function __construct()
    {
        $config = include __DIR__ . '/config.php';

        $this->conn = new mysqli(
            $config['host'],
            $config['user'],
            $config['pass'],
            $config['db']
        );

        if ($this->conn->connect_error) {
            die("資料庫連線失敗：" . $this->conn->connect_error);
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }
}
