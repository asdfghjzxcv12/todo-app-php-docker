<?php
// TODO/app/index.php

// エラー報告を有効にする（開発中はこれを入れておくと便利）
error_reporting(E_ALL);
ini_set('display_errors', 1);

// グローバルな例外ハンドラを登録
set_exception_handler(function ($exception) {
    http_response_code(500);
    error_log("Uncaught Exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine());
    // デバッグ時は詳細なエラーメッセージを直接出力
    echo json_encode(['error' => 'Internal Server Error', 'details' => $exception->getMessage() . ' in ' . $exception->getFile() . ' on line ' . $exception->getLine()]);
    exit();
});

// グローバルなエラーハンドラを登録
set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        // 現在のエラー報告レベルに含まれていないエラーは無視
        return false;
    }
    http_response_code(500);
    error_log("PHP Error: " . $message . " in " . $file . " on line " . $line);
    // デバッグ時は詳細なエラーメッセージを直接出力
    echo json_encode(['error' => 'Internal Server Error', 'details' => $message . ' in ' . $file . ' on line ' . $line]);
    exit();
}, E_ALL);


// TodoControllerを読み込む
require_once __DIR__ . '/Controllers/TodoController.php';

// リクエストのURI（URLのパス部分）を取得
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// リクエストメソッドを取得 (GET, POST, PUT, DELETEなど)
$requestMethod = $_SERVER['REQUEST_METHOD'];

// TodoControllerのインスタンスを作成
$controller = new TodoController();

// ルーティングの定義
switch ($requestUri) {
    case '/api/todos':
        switch ($requestMethod) {
            case 'GET':
                // 全てのTodoアイテムを取得
                $controller->index();
                break;
            case 'POST':
                // 新しいTodoアイテムを作成
                $controller->create();
                break;
            case 'PUT':
                // Todoアイテムを更新
                $controller->update();
                break;
            case 'DELETE':
                // Todoアイテムを削除
                $controller->delete();
                break;
            default:
                // 未対応のメソッド
                http_response_code(405); // Method Not Allowed
                echo json_encode(['error' => 'Method Not Allowed']);
                break;
        }
        break;
    default:
        // それ以外のURLは404 Not Found
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
        break;
}
