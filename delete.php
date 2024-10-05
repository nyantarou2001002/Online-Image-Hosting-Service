<?php
$mysqli = new mysqli("127.0.0.1", "root", "password", "image_hosting", 3307);

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

    if ($image_path) {
        // 画像を削除
        if (unlink($image_path)) {
            // データベースから削除
            $stmt = $mysqli->prepare("DELETE FROM images WHERE delete_token = ?");
            $stmt->bind_param("s", $delete_token);
            $stmt->execute();
            $stmt->close();

            echo "画像が削除されました。";
        } else {
            echo "画像ファイルの削除に失敗しました。";
        }
    } else {
        echo "画像が見つかりませんでした。";
    }
} else {
    echo "不正なリクエストです。";
}
