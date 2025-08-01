<?php
// TODO/app/Controllers/TodoController.php

// Todoモデルを読み込む
require_once __DIR__ . '/../Models/Todo.php'; // Models/Todo.php は Controllers ディレクトリの1つ上の階層にある Models ディレクトリ内
require_once __DIR__ . '/../Database.php'; // Database.php は Controllers ディレクトリの1つ上の階層にある

class TodoController
{
    private $todo; // Todoモデルのインスタンス

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        // データベース接続を取得
        $database = new Database(); // ここで Database クラスが使われる
        $db = $database->getConnection();

        // Todoモデルを初期化
        $this->todo = new Todo($db);
    }

    /**
     * 全てのTodoアイテムを取得してJSONで返す
     */
    public function index()
    {
        $stmt = $this->todo->readAll();
        $num = $stmt->rowCount();

        // Todoアイテムが存在する場合
        if ($num > 0) {
            $todos_arr = array();
            $todos_arr["todos"] = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row); // $row['id']などを直接$idとして使えるようにする

                $todo_item = array(
                    "id" => $id,
                    "title" => $title,
                    "completed" => (bool)$completed, // boolean型にキャスト
                    "created_at" => $created_at
                );
                array_push($todos_arr["todos"], $todo_item);
            }

            // JSON形式で出力
            http_response_code(200);
            echo json_encode($todos_arr["todos"]);
        } else {
            // Todoアイテムが存在しない場合
            http_response_code(200); // 200 OKを返す
            echo json_encode(array()); // 空の配列を返す
        }
    }

    /**
     * 新しいTodoアイテムを作成する
     */
    public function create()
    {
        // リクエストボディからJSONデータを取得
        $data = json_decode(file_get_contents("php://input"));

        // データが不足している場合はエラーを返す
        if (empty($data->title)) {
            http_response_code(400); // Bad Request
            echo json_encode(array("message" => "Unable to create todo. Data is incomplete."));
            return;
        }

        $this->todo->title = $data->title;
        $this->todo->completed = isset($data->completed) ? (bool)$data->completed : false;

        if ($this->todo->create()) {
            http_response_code(201); // Created
            echo json_encode(array("message" => "Todo was created."));
        } else {
            http_response_code(503); // Service Unavailable
            echo json_encode(array("message" => "Unable to create todo."));
        }
    }

    /**
     * Todoアイテムを更新する
     */
    public function update()
    {
        // リクエストボディからJSONデータを取得
        $data = json_decode(file_get_contents("php://input"));

        // IDとcompletedが不足している場合はエラーを返す
        // titleは必須ではなくなった
        if (empty($data->id) || !isset($data->completed)) {
            http_response_code(400); // Bad Request
            echo json_encode(array("message" => "Unable to update todo. ID or completed status is missing."));
            return;
        }

        $this->todo->id = $data->id;

        // まず既存のTodoアイテムを読み込む
        $this->todo->readOne();

        // Todoが見つからない場合はエラー
        if (empty($this->todo->title)) { // readOne()が失敗した場合、titleは空のまま
            http_response_code(404); // Not Found
            echo json_encode(array("message" => "Todo not found."));
            return;
        }

        // リクエストにtitleが含まれていれば更新、なければ既存のtitleを使用
        $this->todo->title = isset($data->title) && !empty($data->title) ? $data->title : $this->todo->title;
        $this->todo->completed = (bool)$data->completed;

        if ($this->todo->update()) {
            http_response_code(200); // OK
            echo json_encode(array("message" => "Todo was updated."));
        } else {
            http_response_code(503); // Service Unavailable
            echo json_encode(array("message" => "Unable to update todo."));
        }
    }

    /**
     * Todoアイテムを削除する
     */
    public function delete()
    {
        // リクエストボディからJSONデータを取得
        $data = json_decode(file_get_contents("php://input"));

        // IDが不足している場合はエラーを返す
        if (empty($data->id)) {
            http_response_code(400); // Bad Request
            echo json_encode(array("message" => "Unable to delete todo. ID is missing."));
            return;
        }

        $this->todo->id = $data->id;

        if ($this->todo->delete()) {
            http_response_code(200); // OK
            echo json_encode(array("message" => "Todo was deleted."));
        } else {
            http_response_code(503); // Service Unavailable
            echo json_encode(array("message" => "Unable to delete todo."));
        }
    }
}
