<?php
// TODO/app/Database.php

class Database
{
    private $host = 'mysql'; // docker-compose.ymlで定義したMySQLサービスのホスト名
    private $db_name = 'testdb'; // docker-compose.ymlで定義したデータベース名
    private $username = 'root'; // docker-compose.ymlで定義したユーザー名
    private $password = 'password'; // docker-compose.ymlで定義したパスワード
    public $conn;

    /**
     * データベース接続を取得する
     *
     * @return PDO データベース接続オブジェクト
     */
    public function getConnection()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            // 接続失敗時はエラーメッセージを出力して終了
            error_log("Database connection error: " . $exception->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Database connection failed.']);
            exit();
        }

        return $this->conn;
    }
}

