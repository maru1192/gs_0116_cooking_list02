<?php
// エラー表示を有効化（デバッグ用）
ini_set('display_errors', 1);
error_reporting(E_ALL);
//デバッグここまで

session_start();
require_once __DIR__ . '/../assets/func.php';

// ログインチェック（必要に応じてコメントアウト）
sschk();

$uid = $_SESSION['user_id'] ?? 0;

//DB接続
$pdo = db_conn();

// 投稿したレシピを取得
$stmt = $pdo->prepare("
    SELECT r.* 
    FROM recipe_list r
    WHERE uid = :uid
    ORDER BY created_at DESC
");

$stmt->bindValue(':uid', $uid, PDO::PARAM_INT);
$status = $stmt->execute();

//データ表示
$recipes = [];

if ($status === false) {
    $error = $stmt->errorInfo();
    exit("ErrorQuery:" . $error[2]);
} else {
    while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $recipes[] = $result;
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>投稿したレシピ一覧 - みんなのズボラ飯</title>
    <link rel="stylesheet" href="/gs_code/gs_0108_cooking_list/assets/css/reset.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="page">
        <header class="page-header">
            <h1>My投稿レシピ一覧</h1>
            <ul class="button_area">
                <li><a href="index.php">一覧に戻る</a></li>
            </ul>
        </header>

        <?php if (empty($recipes)): ?>
            <p class="empty">投稿したレシピはまだありません</p>
        <?php else: ?>
            <div class="recipes-row" aria-label="投稿したレシピ一覧">
                <?php foreach ($recipes as $recipe): ?>
                    <?php
                    $recipeId = $recipe['recipe_id'] ?? '';
                    $img = trim((string)($recipe['image'] ?? ''));
                    $title = trim((string)($recipe['title'] ?? ''));
                    $work_time = $recipe['work_time'] ?? '';
                    $dish_level = $recipe['dish_level'] ?? '';
                    ?>

                    <a href="recipe_detail.php?id=<?= h($recipeId) ?>" class="recipe-card">
                        <div class="thumb">
                            <?php if ($img !== ''): ?>
                                <img src="<?= h($img) ?>" alt="<?= h($title) ?>">
                            <?php else: ?>
                                <div class="thumb-noimg">No Image</div>
                            <?php endif; ?>
                        </div>

                        <div class="card-body">
                            <div class="host">
                                <div class="avatar" aria-hidden="true">実働<?= h($work_time) ?>分</div>
                                <div class="host-name">洗い物Lv<?= h($dish_level) ?></div>
                            </div>

                            <div class="date-line"><?= h($title) ?></div>
                            <div class="time-line"><?= h($recipe['faction'] ?? '') ?></div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>
