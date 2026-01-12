<?php
session_start();
require_once __DIR__ . '/../assets/func.php';

// ログインチェック
sschk();

// JSON形式でレスポンス
header('Content-Type: application/json');

// recipe_idの取得
$recipe_id = filter_input(INPUT_POST, 'recipe_id', FILTER_VALIDATE_INT);
if (!$recipe_id) {
    echo json_encode(['success' => false, 'message' => 'レシピIDが不正です']);
    exit;
}

$uid = $_SESSION['user_id'] ?? 0;
if (!$uid) {
    echo json_encode(['success' => false, 'message' => 'ログインが必要です']);
    exit;
}

try {
    $pdo = db_conn();
    
    // 既にお気に入りかチェック
    $stmt = $pdo->prepare("SELECT favorite_id FROM favorites WHERE uid = :uid AND recipe_id = :recipe_id");
    $stmt->bindValue(':uid', $uid, PDO::PARAM_INT);
    $stmt->bindValue(':recipe_id', $recipe_id, PDO::PARAM_INT);
    $stmt->execute();
    $exists = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($exists) {
        // お気に入りを削除
        $stmt = $pdo->prepare("DELETE FROM favorites WHERE uid = :uid AND recipe_id = :recipe_id");
        $stmt->bindValue(':uid', $uid, PDO::PARAM_INT);
        $stmt->bindValue(':recipe_id', $recipe_id, PDO::PARAM_INT);
        $stmt->execute();
        
        echo json_encode(['success' => true, 'isFavorite' => false]);
    } else {
        // お気に入りに追加
        $stmt = $pdo->prepare("INSERT INTO favorites (uid, recipe_id) VALUES (:uid, :recipe_id)");
        $stmt->bindValue(':uid', $uid, PDO::PARAM_INT);
        $stmt->bindValue(':recipe_id', $recipe_id, PDO::PARAM_INT);
        $stmt->execute();
        
        echo json_encode(['success' => true, 'isFavorite' => true]);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'データベースエラー: ' . $e->getMessage()]);
}
