<?php
$mysqli = new mysqli("127.0.0.1", "root", "password", "image_hosting", 3307);

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

        // 画像を表示
        echo "<img src='$image_path' alt='アップロード画像'>";
        echo "<p>ビューカウント: " . ($view_count + 1) . "</p>";
    } else {
        echo "画像が見つかりませんでした。";
    }
} else {
    echo "不正なリクエストです。";
}
