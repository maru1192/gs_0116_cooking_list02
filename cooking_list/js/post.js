// みんなのズボラ飯 - 投稿フォーム制御

$(function() {
    
    // ========================================
    // 1. 画像プレビュー
    // ========================================
    $('#imageInput').on('change', function(e) {
        const file = e.target.files[0];
        
        if (file) {
            // ファイルサイズチェック（5MB）
            if (file.size > 5 * 1024 * 1024) {
                alert('画像サイズは5MB以内にしてください');
                $(this).val('');
                $('#imagePreview').html('');
                return;
            }
            
            // プレビュー表示
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').html(`
                    <img src="${e.target.result}" alt="プレビュー" class="image-preview__img">
                    <button type="button" class="image-preview__remove" onclick="removeImage()">削除</button>
                `);
            };
            reader.readAsDataURL(file);
        } else {
            $('#imagePreview').html('');
        }
    });
    
    
    // ========================================
    // 2. フォーム送信前のバリデーション
    // ========================================
    $('#postForm').on('submit', function(e) {
        // 手順が最低1つ入力されているか
        const steps = $('input[name="steps[]"]').filter(function() {
            return $(this).val().trim() !== '';
        });
        
        if (steps.length === 0) {
            alert('手順を最低1つ入力してください');
            e.preventDefault();
            return false;
        }
    });
    
    
    // ========================================
    // 3. ラジオボタン・チェックボックスのスタイル切替
    // ========================================
    
    // ラジオボタンのアクティブスタイル
    $('input[type="radio"]').on('change', function() {
        const name = $(this).attr('name');
        $(`input[name="${name}"]`).closest('label').removeClass('active');
        $(this).closest('label').addClass('active');
    });
    
    // 初期表示
    $('input[type="radio"]:checked').each(function() {
        $(this).closest('label').addClass('active');
    });
    
    // チェックボックスのアクティブスタイル
    $('input[type="checkbox"]').on('change', function() {
        $(this).closest('label').toggleClass('active', $(this).is(':checked'));
    });
    
});


// ========================================
// グローバル関数：画像削除
// ========================================
function removeImage() {
    $('#imageInput').val('');
    $('#imagePreview').html('');
}
