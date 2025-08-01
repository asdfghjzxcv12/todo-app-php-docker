<?php
// TODO/app/Models/Todo.php

require_once __DIR__ . '/../Database.php'; // Database.phpを読み込む

class Todo
{
    private $conn; // データベース接続
    private $table_name = "todos"; // テーブル名

    // Todoオブジェクトのプロパティ
    public $id;
    public $title;
    public $completed;
    public $created_at;

    /**
     * コンストラクタ
     * @param PDO $db データベース接続オブジェクト
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * 全てのTodoアイテムを取得する
     * @return PDOStatement Todoアイテムのリスト
     */
    public function readAll()
    {
        // 全てのTodoアイテムを選択するクエリ
        $query = "SELECT id, title, completed, created_at FROM " . $this->table_name . " ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    /**
     * 単一のTodoアイテムを取得する
     * @return void
     */
    public function readOne()
    {
        // 単一のTodoアイテムを選択するクエリ
        $query = "SELECT id, title, completed, created_at FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // プロパティに値を設定
        if ($row) {
            $this->title = $row['title'];
            $this->completed = $row['completed'];
            $this->created_at = $row['created_at'];
        }
    }

    /**
     * 新しいTodoアイテムを作成する
     * @return bool 成功した場合はtrue、失敗した場合はfalse
     */
    public function create()
    {
        // 挿入クエリ
        $query = "INSERT INTO " . $this->table_name . " SET title=:title, completed=:completed, created_at=:created_at";

        $stmt = $this->conn->prepare($query);

        // HTMLからの特殊文字をサニタイズ
        $this->title = htmlspecialchars(strip_tags($this->title));
        // completed は boolean (true/false) なので、int にキャストして 0 または 1 にする
        $this->completed = (int)$this->completed;
        $this->created_at = date('Y-m-d H:i:s'); // 現在の日時を設定

        // パラメータをバインド
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":completed", $this->completed);
        $stmt->bindParam(":created_at", $this->created_at);

        // クエリを実行
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    /**
     * Todoアイテムを更新する
     * @return bool 成功した場合はtrue、失敗した場合はfalse
     */
    public function update()
    {
        // 更新クエリ
        $query = "UPDATE " . $this->table_name . " SET title=:title, completed=:completed WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        // HTMLからの特殊文字をサニタイズ
        $this->title = htmlspecialchars(strip_tags($this->title));
        // completed は boolean (true/false) なので、int にキャストして 0 または 1 にする
        $this->completed = (int)$this->completed;
        $this->id = htmlspecialchars(strip_tags($this->id));

        // パラメータをバインド
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":completed", $this->completed);
        $stmt->bindParam(":id", $this->id);

        // クエリを実行
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    /**
     * Todoアイテムを削除する
     * @return bool 成功した場合はtrue、失敗した場合はfalse
     */
    public function delete()
    {
        // 削除クエリ
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

        $stmt = $this->conn->prepare($query);

        // HTMLからの特殊文字をサニタイズ
        $this->id = htmlspecialchars(strip_tags($this->id));

        // パラメータをバインド
        $stmt->bindParam(1, $this->id);

        // クエリを実行
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}



