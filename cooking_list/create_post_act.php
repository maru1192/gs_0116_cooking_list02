<?php
session_start();
require_once __DIR__ . '/../assets/func.php';

// ログインチェック
sschk();

// ========================================
// POSTデータ取得
// ========================================

// エラー配列の初期化
$errors = [];

// 基本情報
$title = $_POST['title'] ?? '';

// 実働時間
$work_time = $_POST['work_time'] ?? '';

// 材料
$materials = $_POST['materials'] ?? '';

// 手順（配列）
$steps = $_POST['steps'] ?? [];
// 空の要素を除外
$steps = array_filter($steps, fn($s) => !empty(trim($s)));

// 使用する道具（配列）
$tools = $_POST['tools'] ?? [];

// 洗い物レベル
$dish_level = $_POST['dish_level'] ?? '';

// 派閥
$faction = $_POST['faction'] ?? '';


// ========================================
// 画像アップロード処理
// ========================================
$image_path = null;

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    echo '<p style="color: green;">★画像アップロード処理に入りました</p>';
    
    $file = $_FILES['image'];
    
    // ファイルサイズチェック（5MB）
    echo 'ファイルサイズ: ' . $file['size'] . ' bytes<br>';
    if ($file['size'] > 5 * 1024 * 1024) {
        $errors[] = '画像サイズは5MB以内にしてください';
        echo '<p style="color: red;">サイズエラー</p>';
    } else {
        echo '<p style="color: green;">サイズOK</p>';
    }
    
    // MIME typeチェック
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    echo 'MIME type: ' . $mime_type . '<br>';
    
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($mime_type, $allowed_types)) {
        $errors[] = '画像形式はJPEG, PNG, GIF, WebPのみ対応しています';
        echo '<p style="color: red;">MIME typeエラー</p>';
    } else {
        echo '<p style="color: green;">MIME type OK</p>';
    }
    
    // 拡張子チェック
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    echo '拡張子: ' . $ext . '<br>';
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
        $errors[] = '画像拡張子が不正です';
        echo '<p style="color: red;">拡張子エラー</p>';
    } else {
        echo '<p style="color: green;">拡張子OK</p>';
    }
    
    echo 'エラー数: ' . count($errors) . '<br>';
    
    // エラーがなければ保存
    if (empty($errors)) {
        echo '<p style="color: green;">★保存処理に入りました</p>';
        
        $upload_dir = __DIR__ . '/uploads/recipes/';
        echo 'アップロードディレクトリ: ' . $upload_dir . '<br>';
        
        // ディレクトリがなければ作成
        if (!is_dir($upload_dir)) {
            echo 'ディレクトリを作成します<br>';
            if (mkdir($upload_dir, 0777, true)) {
                echo 'ディレクトリ作成成功<br>';
            } else {
                echo '<p style="color: red;">ディレクトリ作成失敗</p>';
            }
        } else {
            echo 'ディレクトリは存在します<br>';
        }
        
        // ディレクトリの権限確認
        if (is_writable($upload_dir)) {
            echo '<p style="color: green;">ディレクトリは書き込み可能</p>';
        } else {
            echo '<p style="color: red;">ディレクトリに書き込み権限がありません</p>';
            echo '現在の権限: ' . substr(sprintf('%o', fileperms($upload_dir)), -4) . '<br>';
        }
        
        // ファイル名生成（ユニーク）
        $filename = uniqid('recipe_', true) . '.' . $ext;
        $upload_path = $upload_dir . $filename;
        
        // デバッグ: パス確認
        error_log('アップロード先: ' . $upload_path);
        
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            $image_path = 'uploads/recipes/' . $filename; // DB保存用の相対パス
            echo '<p style="color: green; font-weight: bold;">★★★ 画像保存成功: ' . $image_path . '</p>';
            error_log('画像保存成功: ' . $image_path);
        } else {
            $errors[] = '画像のアップロードに失敗しました';
            echo '<p style="color: red; font-weight: bold;">画像保存失敗</p>';
            echo 'エラー: ' . error_get_last()['message'] ?? 'unknown' . '<br>';
            error_log('画像保存失敗');
        }
    } else {
        echo '<p style="color: red;">バリデーションエラーがあります</p>';
        error_log('バリデーションエラー: ' . print_r($errors, true));
    }
} else {
    echo '<p style="color: orange;">画像アップロード処理に入っていません</p>';
    // 画像が選択されていない、またはエラーがある場合
    if (isset($_FILES['image'])) {
        echo 'エラーコード: ' . $_FILES['image']['error'] . '<br>';
        error_log('画像アップロードエラーコード: ' . $_FILES['image']['error']);
    }
}

// エラーがあれば処理を中断
if (!empty($errors)) {
    echo '<h2>エラーが発生しました</h2>';
    echo '<ul>';
    foreach ($errors as $error) {
        echo '<li>' . h($error) . '</li>';
    }
    echo '</ul>';
    echo '<a href="javascript:history.back()">戻る</a>';
    exit;
}

// ========================================
// DB登録処理
// ========================================
try {
    $pdo = db_conn();
    
    // user_id（セッションから取得）
    $user_id = $_SESSION['user_id'];
    
    // recipe_listテーブルに登録
    $stmt = $pdo->prepare("INSERT INTO 
                                recipe_list (uid, title, image, work_time, dish_level, materials, steps, tools, faction)
                            VALUES
                                (:uid, :title, :image, :work_time, :dish_level, :materials, :steps, :tools, :faction)");

    // バインド変数に保存
    $stmt->bindValue(':uid', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':title', $title, PDO::PARAM_STR);
    $stmt->bindValue(':image', $image_path, PDO::PARAM_STR);
    $stmt->bindValue(':work_time', $work_time, PDO::PARAM_INT);
    $stmt->bindValue(':dish_level', $dish_level, PDO::PARAM_INT);
    $stmt->bindValue(':materials', $materials, PDO::PARAM_STR);
    $stmt->bindValue(':steps', json_encode(array_values($steps), JSON_UNESCAPED_UNICODE), PDO::PARAM_STR);
    $stmt->bindValue(':tools', json_encode($tools, JSON_UNESCAPED_UNICODE), PDO::PARAM_STR);
    $stmt->bindValue(':faction', $faction, PDO::PARAM_STR);

    // 実行
    $status = $stmt->execute();
    
    // データ登録処理後
    if($status === false){
        // SQL実行時にエラーがある場合
        $error = $stmt->errorInfo();
        exit('ErrorMessage: '.$error[2]);
    }
    
    // 完了画面へリダイレクト
    header("Location: complete.php");
    exit;
    
} catch (Exception $e) {
    exit('登録エラー: ' . h($e->getMessage()));
}