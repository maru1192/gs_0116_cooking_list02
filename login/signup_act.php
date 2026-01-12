<?php
//最初にセッションを開始
session_start();

//関数ファイルの読み込み
require_once __DIR__ . '/../assets/func.php';

//1. POSTデータ取得
$lastName = $_POST['lastName'] ?? '';
$firstName = $_POST['firstName'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// 入力値チェック
if (empty($lastName) || empty($firstName) || empty($email) || empty($password)) {
    exit('必須項目が入力されていません');
}

//2. DB接続します
$pdo = db_conn();

//３．データ登録SQL作成

//パスワードをハッシュ保存（セキュリティ対策）
$hashed = password_hash($password, PASSWORD_DEFAULT);

// 1. SQL文を用意
$stmt = $pdo->prepare("INSERT INTO 
                            user_table(name_sei, name_mei, lid, lpw) 
                        VALUES
                            (:lastName, :firstName, :email, :password)");


//  2. バインド変数を用意
// Integer 数値の場合 PDO::PARAM_INT
// String文字列の場合 PDO::PARAM_STR
$stmt->bindValue(':lastName', $lastName, PDO::PARAM_STR);
$stmt->bindValue(':firstName', $firstName, PDO::PARAM_STR);
$stmt->bindValue(':email', $email, PDO::PARAM_STR);
$stmt->bindValue(':password', $hashed, PDO::PARAM_STR);

//  3. 実行
$status = $stmt->execute();

//４．データ登録処理後
if ($status === false) {
    //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
    $error = $stmt->errorInfo();
    exit('ErrorMessage:' . $error[2]);
} else {
    //５．登録成功時、セッションに値を設定
    $_SESSION['chk_ssid'] = session_id();
    $_SESSION['name_sei'] = $lastName;
    $_SESSION['name_mei'] = $firstName;
    $_SESSION['user_id'] = (int)$pdo->lastInsertId();
    
    //リダイレクト
    redirect('../cooking_list/index.php');
}