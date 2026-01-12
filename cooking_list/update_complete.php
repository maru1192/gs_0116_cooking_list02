<?php
session_start();
require_once __DIR__ . '/../assets/func.php';

// ログインチェック
sschk();

// ユーザーidの取得
$recipe_id = $_GET['id'] ?? null;

// データ取得
$pdo = db_conn();

// 投稿したレシピを取得
$stmt = $pdo->prepare("SELECT * FROM recipe_list WHERE recipe_id = :recipe_id");
$stmt->bindValue(':recipe_id', $recipe_id, PDO::PARAM_INT);
$status = $stmt->execute();

if ($status === false) {
    $error = $stmt->errorInfo();
    exit("ErrorQuery:" . $error[2]);
}

$result = $stmt->fetch(PDO::FETCH_ASSOC);



?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>更新完了 - みんなのズボラ飯</title>
    <link rel="stylesheet" href="/gs_code/gs_0108_cooking_list/assets/css/reset.css">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .complete-card {
            border-radius: 16px;
            padding: 48px 32px;
            text-align: center;
            max-width: 500px;
        }
        
        .complete-icon {
            font-size: 80px;
            margin-bottom: 24px;
            animation: popIn 0.5s ease-out;
        }
        
        @keyframes popIn {
            0% { transform: scale(0); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        
        .complete-title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 16px;
            color: #333;
        }
        
        .complete-message {
            font-size: 16px;
            color: #666;
            margin-bottom: 32px;
            line-height: 1.6;
        }
        
        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .btn {
            display: block;
            padding: 16px 32px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background: #FF6B6B;
            color: white;
        }
        
        .btn-primary:hover {
            background: #ee5555;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 107, 107, 0.4);
        }
        
        .btn-secondary {
            background: #f8f9fa;
            color: #333;
        }
        
        .btn-secondary:hover {
            background: #e9ecef;
        }
    </style>
</head>
<body>
    <div class="complete-card">
        <div class="complete-icon">🎉</div>
        <h1 class="complete-title">更新完了！</h1>
        <p class="complete-message">
            あなたのズボラ飯<br>
            『<?= h($result['title']) ?>』<br>
            が更新されました。
        </p>
        <div class="btn-group">
            <a href="index.php" class="btn btn-primary">みんなの投稿を見る</a>
            <a href="recipe_detail.php?id=<?= h($recipe_id) ?>" class="btn btn-secondary">修正した投稿内容を見る</a>
        </div>
    </div>
</body>
</html>
