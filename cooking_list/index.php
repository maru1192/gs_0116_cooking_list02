<?php
session_start();
require_once __DIR__ . '/../assets/func.php';

// ログインチェック（必要に応じてコメントアウト）
sschk();

//DB接続
try {
    $pdo = db_conn();
} catch (PDOException $e) {
    exit('DBConnectError' . $e->getMessage());
}

//データ取得SQL作成（recipe_listテーブルから取得）
$stmt = $pdo->prepare("SELECT * FROM recipe_list ORDER BY created_at DESC");
$status = $stmt->execute();


//３．データ表示
$recipes = [];

if ($status === false) {
    //execute（SQL実行時にエラーがある場合）
    $error = $stmt->errorInfo();
    exit("ErrorQuery:" . $error[2]);
} else {
    //Selectデータの数だけ自動でループしてくれる
    //FETCH_ASSOC=http://php.net/manual/ja/pdostatement.fetch.php
    while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $recipes[] = $result;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>みんなのズボラ飯</title>
    <link rel="stylesheet" href="/gs_code/gs_0108_cooking_list/assets/css/reset.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="page">
        <header class="page-header">
            <h1>みんなのズボラ飯</h1>
            <ul class="button_area">
                <li><a href="create_post.php">新規レシピ登録</a></li>
                <li><a href="my_recipe.php">投稿したレシピ</a></li>
                <li><a href="favorite.php">★お気に入り</a></li>
            </ul>
        </header>

        <div class="recipes-row" aria-label="レシピ一覧">
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
    </div>
</body>

</html>