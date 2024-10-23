
<?php
// データベース接続
$mysqli = new mysqli("127.0.0.1", "root", "", "image_hosting", 3306);

if ($mysqli->connect_error) {
    die("データベース接続エラー");
}

if (isset($_GET['id'])) {
    $image_id = intval($_GET['id']);

    // 画像パスとビューカウントを取得
    $stmt = $mysqli->prepare("SELECT image_path, view_count FROM images WHERE id = ?");
    $stmt->bind_param("i", $image_id);
    $stmt->execute();
    $stmt->bind_result($image_path, $view_count);
    $stmt->fetch();

    // クエリの結果セットを閉じる
    $stmt->close();

    if ($image_path) {
        // ビューカウントを更新
        $stmt = $mysqli->prepare("UPDATE images SET view_count = view_count + 1 WHERE id = ?");
        $stmt->bind_param("i", $image_id);
        $stmt->execute();
        $stmt->close();

        // Tailwind CSSを使用してHTMLを表示
        echo '<!DOCTYPE html>
        <html lang="ja">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>画像表示</title>
            <script src="https://cdn.tailwindcss.com"></script>
        </head>
        <body class="bg-gray-100 min-h-screen flex items-center justify-center">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
                <h1 class="text-2xl font-bold mb-4 text-center text-gray-700">アップロード画像</h1>
                <div class="mb-4">
                    <img src="/onlineImages/uploads/' . basename($image_path) . '" alt="アップロード画像" class="w-full rounded-lg shadow">
                </div>
                <p class="text-gray-700 text-center">ビューカウント: <span class="font-semibold">' . ($view_count + 1) . '</span></p>
            </div>
        </body>
        </html>';
    } else {
        echo '<!DOCTYPE html>
        <html lang="ja">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>エラー</title>
            <script src="https://cdn.tailwindcss.com"></script>
        </head>
        <body class="bg-gray-100 min-h-screen flex items-center justify-center">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
                <p class="text-red-500 text-center">画像が見つかりませんでした。</p>
            </div>
        </body>
        </html>';
    }
} else {
    echo '<!DOCTYPE html>
    <html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>エラー</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-100 min-h-screen flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
            <p class="text-red-500 text-center">不正なリクエストです。</p>
        </div>
    </body>
    </html>';
}
?>