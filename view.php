<?php
// データベース接続
$mysqli = new mysqli("127.0.0.1", "root", "", "image_hosting", 3306); // ポートを3306に変更


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

        // 画像を表示するHTML
        echo "<h1>アップロード画像</h1>";
        //echo "<img src='$image_path' alt='アップロード画像' style='max-width: 100%; height: auto;'>";  // 画像表示の修正
        $image_url = "/onlineImages/uploads/" . basename($image_path);
        echo "<img src='$image_url' alt='アップロード画像' style='max-width: 100%; height: auto;'>";

        echo "<p>ビューカウント: " . ($view_count + 1) . "</p>";
    } else {
        echo "画像が見つかりませんでした。";
    }
} else {
    echo "不正なリクエストです。";
}
