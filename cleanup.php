<?php
$mysqli = new mysqli("127.0.0.1", "root", "password", "image_hosting", 3307);

if ($mysqli->connect_error) {
    die("データベース接続エラー");
}

// 30日以上アクセスされていない画像を取得
$threshold_date = date('Y-m-d H:i:s', strtotime('-30 days'));
$stmt = $mysqli->prepare("SELECT image_path FROM images WHERE upload_date < ?");
$stmt->bind_param("s", $threshold_date);
$stmt->execute();
$stmt->bind_result($image_path);

while ($stmt->fetch()) {
    // 画像ファイルを削除
    if (file_exists($image_path)) {
        unlink($image_path);
    }
}

// データベースから古いレコードを削除
$stmt = $mysqli->prepare("DELETE FROM images WHERE upload_date < ?");
$stmt->bind_param("s", $threshold_date);
$stmt->execute();

$stmt->close();
$mysqli->close();

echo "30日以上アクセスされていない画像が削除されました。\n";
