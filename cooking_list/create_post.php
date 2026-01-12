<?php
session_start();
require_once __DIR__ . '/../assets/func.php';

// ログインチェック（必要に応じてコメントアウト）
sschk();

// データ取得
$pdo = db_conn();

// 使う道具取得（DBに入る値 => 表示ラベル）
$tools = [
    '包丁' => '包丁',
    'キッチンバサミ' => 'キッチンバサミ',
    'まな板' => 'まな板',
    'ボウル' => 'ボウル',
    'レンジ' => 'レンジ',
    'オーブン' => 'オーブン',
    'フライパン' => 'フライパン',
    '鍋' => '鍋',
    'ガスコンロ（IHコンロ）' => 'ガスコンロ（IHコンロ）',
    '炊飯器' => '炊飯器',
    '電気ケトル' => '電気ケトル',
];

// 派閥取得
$factions = [
    'ワンパン派' => 'ワンパン派',
    '包丁なし派' => '包丁なし派',
    'まとめ調理冷凍派' => 'まとめ調理冷凍派',
    '炊飯器派' => '炊飯器派',
];
?>


<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>投稿する - みんなのズボラ飯</title>
    <link rel="stylesheet" href="/gs_code/gs_0108_cooking_list/assets/css/reset.css">
    <link rel="stylesheet" href="css/post.css">
</head>

<body>
    <div class="container">
        <ul class="button_area">
                <li><a href="index.php">戻る</a></li>
            </ul>
        <header class="header">
            <h1 class="header__title">レシピを投稿する</h1>
        </header>


        <!-- ========================================
                    レシピ投稿専用エリア
        ========================================= -->

        <form id="postForm" action="create_post_act.php" method="POST" enctype="multipart/form-data">
            <!-- タイトル（共通） -->
            <section class="section">
                <label class="label">
                    タイトル <span class="required">必須</span>
                </label>
                <input type="text" name="title" class="input" placeholder="例：レンチン3分のカレー" maxlength="100" required>
            </section>

            <div id="recipeFields">

                <!-- 写真アップロード -->
                <section class="section">
                    <label class="label">
                        写真 <span class="optional">任意</span>
                    </label>

                    <div class="image-upload">
                        <input type="file" name="image" id="imageInput" accept="image/*" class="image-upload__input">
                        <label for="imageInput" class="image-upload__label">
                            <span class="image-upload__icon">📷</span>
                            <span class="image-upload__text">写真を選択（5MB以内）</span>
                        </label>
                        <div id="imagePreview" class="image-preview"></div>
                    </div>
                </section>

                <!-- 実働時間 -->
                <section class="section">
                    <label class="label">
                        実働時間 <span class="required">必須</span>
                    </label>
                    <div class="button-group">
                        <label class="btn-option">
                            <input type="radio" name="work_time" value="5" required>
                            <span>5分</span>
                        </label>
                        <label class="btn-option">
                            <input type="radio" name="work_time" value="10">
                            <span>10分</span>
                        </label>
                        <label class="btn-option">
                            <input type="radio" name="work_time" value="15">
                            <span>15分</span>
                        </label>
                        <label class="btn-option">
                            <input type="radio" name="work_time" value="30">
                            <span>30分</span>
                        </label>
                    </div>
                </section>

                <!-- 材料 -->
                <section class="section">
                    <label class="label">
                        材料 <span class="optional">任意</span>
                    </label>
                    <textarea name="materials" class="textarea" rows="4" placeholder="例：&#10;ごはん 1杯&#10;卵 1個&#10;レトルトカレー 1袋"></textarea>
                    <p class="help-text">1行に1つずつ書くと見やすいです</p>
                </section>

                <!-- 手順 -->
                <section class="section">
                    <label class="label">
                        手順 <span class="required">必須</span>
                    </label>
                    <div class="steps-container">
                        <div class="step-item">
                            <span class="step-number">1</span>
                            <input type="text" name="steps[]" class="input" placeholder="ステップ1" required>
                        </div>
                        <div class="step-item">
                            <span class="step-number">2</span>
                            <input type="text" name="steps[]" class="input" placeholder="ステップ2">
                        </div>
                        <div class="step-item">
                            <span class="step-number">3</span>
                            <input type="text" name="steps[]" class="input" placeholder="ステップ3">
                        </div>
                        <div class="step-item">
                            <span class="step-number">4</span>
                            <input type="text" name="steps[]" class="input" placeholder="ステップ4">
                        </div>
                        <div class="step-item">
                            <span class="step-number">5</span>
                            <input type="text" name="steps[]" class="input" placeholder="ステップ5">
                        </div>
                    </div>
                </section>
            </div>

            <!-- ========================================
                    選択肢エリア
            ========================================= -->

            <!-- 使用する道具 -->
            <section class="section">
                <label class="label">
                    使用する道具 <span class="optional">全て選択してください！</span>
                </label>
                <div class="radio-group">
                    <?php foreach ($tools as $key => $label): ?>
                        <label class="checkbox-option">
                            <input type="checkbox" name="tools[]" value="<?= h($key) ?>">
                            <span><?= h($label) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- 洗い物レベル -->
            <section class="section">
                <label class="label">
                    洗い物レベル <span class="required">必須</span>
                </label>
                <div class="button-group button-group--vertical">
                    <label class="btn-option btn-option--large">
                        <input type="radio" name="dish_level" value="0" required>
                        <span>【Lv0】捨てるだけ（包材/紙皿/袋調理）</span>
                    </label>
                    <label class="btn-option btn-option--large">
                        <input type="radio" name="dish_level" value="1">
                        <span>【Lv1】1個だけ（皿1枚/マグ1つ）</span>
                    </label>
                    <label class="btn-option btn-option--large">
                        <input type="radio" name="dish_level" value="2">
                        <span>【Lv2】2〜3個（皿＋箸＋ボウル程度）</span>
                    </label>
                    <label class="btn-option btn-option--large">
                        <input type="radio" name="dish_level" value="3">
                        <span>【Lv3】それ以上（鍋/フライパン/まな板など）</span>
                    </label>
                </div>
            </section>

            <!-- 派閥 -->
            <section class="section">
                <label class="label">
                    派閥 <span class="optional">この料理が所属する派閥を1つ選択してください！</span>
                </label>
                <div class="radio-group">
                    <?php foreach ($factions as $key => $label): ?>
                        <label class="radio-option">
                            <input type="radio" name="faction" value="<?= h($key) ?>">
                            <span><?= h($label) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </section>


            <!-- 投稿ボタン -->
            <div class="submit-area">
                <button type="submit" class="btn-submit">投稿する</button>
            </div>

        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="js/post.js"></script>
</body>

</html>