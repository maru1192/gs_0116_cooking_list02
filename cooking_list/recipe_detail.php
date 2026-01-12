<?php
session_start();
require_once __DIR__ . '/../assets/func.php';

// ログインチェック（必要に応じてコメントアウト）
sschk();

// ---- id 取得（/event_detail.php?id=1）----
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    exit('idが不正です');
}

//DB接続
try {
    $pdo = db_conn();
} catch (PDOException $e) {
    exit('DBConnectError' . $e->getMessage());
}

// ---- 1件取得 ----
$stmt = $pdo->prepare("SELECT * FROM recipe_list WHERE recipe_id = :id");
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$recipe = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$recipe) {
    http_response_code(404);
    exit('レシピが見つかりません');
}

// データを取得
$title = htmlspecialchars($recipe['title'] ?? '', ENT_QUOTES);
$headerImage = trim($recipe['image'] ?? '');
$work_time = intval($recipe['work_time'] ?? 0);
$dish_level = intval($recipe['dish_level'] ?? 1);
$materials = htmlspecialchars($recipe['materials'] ?? '', ENT_QUOTES);
$faction = htmlspecialchars($recipe['faction'] ?? '', ENT_QUOTES);
$created_at = $recipe['created_at'] ?? '';

// JSON形式の配列をデコード
$steps = json_decode($recipe['steps'] ?? '[]', true);
if (!is_array($steps)) $steps = [];

$tools = json_decode($recipe['tools'] ?? '[]', true);
if (!is_array($tools)) $tools = [];

// お気に入り状態をチェック
$uid = $_SESSION['user_id'] ?? 0;
$isFavorite = false;

$stmt = $pdo->prepare("SELECT COUNT(*) FROM favorites WHERE uid = :uid AND recipe_id = :recipe_id");
$stmt->bindValue(':uid', $uid, PDO::PARAM_INT);
$stmt->bindValue(':recipe_id', $id, PDO::PARAM_INT);
$stmt->execute();
$isFavorite = $stmt->fetchColumn() > 0;

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - みんなのズボラ飯</title>
    <link rel="stylesheet" href="/gs_code/gs_0108_cooking_list/assets/css/reset.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/recipe.css">
    <script>
        function toggleFavorite(recipeId) {
            const btn = document.getElementById('favoriteBtn');
            const star = btn.querySelector('.star-icon');
            const text = btn.querySelector('.favorite-text');

            fetch('favorite_toggle.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'recipe_id=' + recipeId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.isFavorite) {
                            star.textContent = '★';
                            text.textContent = 'お気に入り登録済み';
                            btn.classList.add('is-favorite');
                        } else {
                            star.textContent = '☆';
                            text.textContent = 'お気に入りに追加';
                            btn.classList.remove('is-favorite');
                        }
                    } else {
                        alert(data.message || 'エラーが発生しました');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('通信エラーが発生しました');
                });
        }
    </script>
</head>

<body>
    <div class="detail-page">
        <div class="page-top-actions">
            <a href="index.php" class="back-link">← 一覧に戻る</a>
            
            <?php if ($uid === ($recipe['uid'] ?? 0)) : ?>
                <ul class="btn-submit">
                    <li><a href="recipe_edit.php?id=<?= $id ?>">レシピを編集する</a></li>
                    <li><a href="delete.php?id=<?= $id ?>">レシピを削除する</a></li>
                </ul>
            <?php endif; ?>
        </div>

        <div class="recipe-header">
            <?php if ($headerImage): ?>
                <img src="<?= htmlspecialchars($headerImage, ENT_QUOTES) ?>"
                    alt="<?= $title ?>" class="recipe-header-image">
            <?php else: ?>
                <div class="recipe-header-noimg">NO IMAGE</div>
            <?php endif; ?>

            <div class="recipe-header-title-area">
                <h1 class="recipe-title"><?= $title ?></h1>
                <button type="button" id="favoriteBtn" class="favorite-btn <?= $isFavorite ? 'is-favorite' : '' ?>" onclick="toggleFavorite(<?= $id ?>)">
                    <span class="star-icon"><?= $isFavorite ? '★' : '☆' ?></span>
                    <span class="favorite-text"><?= $isFavorite ? 'お気に入り登録済み' : 'お気に入りに追加' ?></span>
                </button>
            </div>

            <div class="recipe-meta">
                <span class="pill">⏱️ <?= $work_time ?>分</span>

                <span class="pill">
                    <?php
                    $levelLabels = [1 => '⭐ 洗い物Lv1', 2 => '⭐⭐ 洗い物Lv2', 3 => '⭐⭐⭐ 洗い物Lv3'];
                    echo $levelLabels[$dish_level] ?? '⭐ 洗い物Lv1'; //正常に表示されないので、後で修正する！！！！
                    ?>
                </span>

                <?php if ($faction): ?>
                    <span class="pill">📍 <?= $faction ?></span>
                <?php endif; ?>
            </div>

            <p class="recipe-date">投稿日: <?= date('Y年m月d日', strtotime($created_at)) ?></p>
        </div>

        <div class="recipe-section">
            <h2>材料</h2>
            <div class="materials-text"><?= nl2br($materials) ?></div>
        </div>

        <div class="recipe-section">
            <h2>作り方</h2>
            <ol class="steps-list">
                <?php foreach ($steps as $step): ?>
                    <li><?= htmlspecialchars($step, ENT_QUOTES) ?></li>
                <?php endforeach; ?>
            </ol>
        </div>

        <div class="recipe-section">
            <h2>使用する道具</h2>
            <div class="tools-list">
                <?php foreach ($tools as $tool): ?>
                    <span class="tool-badge"><?= htmlspecialchars($tool, ENT_QUOTES) ?></span>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</body>

</html>