<?php
session_start();
require_once __DIR__ . '/../assets/func.php';

// ログインチェック（必要に応じてコメントアウト）
sschk();

$id = $_GET['id'];

//2. DB接続します
$pdo = db_conn();

//３．データ登録SQL作成
$stmt = $pdo->prepare('DELETE FROM recipe_list WHERE recipe_id = :id;');
$stmt->bindValue(':id', $id, PDO::PARAM_INT); 
$status = $stmt->execute(); //実行

//４．データ登録処理後
if ($status === false) {
    //*** function化する！******\
    $error = $stmt->errorInfo();
    exit('SQLError:' . print_r($error, true));
} else {
    //*** function化する！*****************
    redirect('index.php');
}
