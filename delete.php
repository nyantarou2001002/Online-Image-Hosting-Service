<?php
$mysqli = new mysqli("127.0.0.1", "root", "", "image_hosting", 3306);

if ($mysqli->connect_error) {
    die("データベース接続エラー");
}

if (isset($_GET['token'])) {
    $delete_token = $_GET['token'];

    // 画像パスを取得
    $stmt = $mysqli->prepare("SELECT image_path FROM images WHERE delete_token = ?");
    $stmt->bind_param("s", $delete_token);
    $stmt->execute();
    $stmt->bind_result($image_path);
    $stmt->fetch();

    // クエリの結果セットを閉じる
    $stmt->close();

    // Tailwind CSSを使用して結果を表示するHTMLを生成
    echo '<!DOCTYPE html>
    <html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>画像削除</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-100 min-h-screen flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">';

    if ($image_path) {
        // 画像を削除
        if (unlink($image_path)) {
            // データベースから削除
            $stmt = $mysqli->prepare("DELETE FROM images WHERE delete_token = ?");
            $stmt->bind_param("s", $delete_token);
            $stmt->execute();
            $stmt->close();

            echo '<div class="bg-green-100 text-green-800 p-4 rounded-lg text-center">
                    <p>画像が削除されました。</p>
                  </div>';
        } else {
            echo '<div class="bg-red-100 text-red-800 p-4 rounded-lg text-center">
                    <p>画像ファイルの削除に失敗しました。</p>
                  </div>';
        }
    } else {
        echo '<div class="bg-red-100 text-red-800 p-4 rounded-lg text-center">
                <p>画像が見つかりませんでした。</p>
              </div>';
    }
} else {
    echo '<div class="bg-red-100 text-red-800 p-4 rounded-lg text-center">
            <p>不正なリクエストです。</p>
          </div>';
}

echo '</div>
    </body>
    </html>';
