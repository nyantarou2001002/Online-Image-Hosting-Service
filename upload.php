<?php
header('Content-Type: application/json; charset=utf-8'); // JSON形式で返す



// エラー表示設定
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// アップロード先ディレクトリ
$target_dir = __DIR__ . "/uploads/";  // 絶対パスに変更
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];

// データベース接続
$mysqli = new mysqli("127.0.0.1", "root", "", "image_hosting", 3306); // ポートを3306に変更


if ($mysqli->connect_error) {
    echo json_encode(['success' => false, 'error' => 'データベース接続エラー: ' . $mysqli->connect_error]);
    exit;
}

// 画像ファイルが正しくアップロードされたか確認
if ($_FILES['image']['error'] == 0) {
    $file_name = basename($_FILES['image']['name']);
    $target_file = $target_dir . uniqid() . '-' . $file_name;
    $file_type = mime_content_type($_FILES['image']['tmp_name']);

    // 許可されたファイル形式か確認
    if (in_array($file_type, $allowed_types)) {
        // ファイルをアップロード先に移動
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            // 削除用のトークンを生成
            $delete_token = bin2hex(random_bytes(16));
            $stmt = $mysqli->prepare("INSERT INTO images (image_name, image_path, delete_token) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $file_name, $target_file, $delete_token);
            $stmt->execute();
            $image_id = $stmt->insert_id;

            // 画像URLと削除URLを生成
            //$image_url = "http://{$_SERVER['HTTP_HOST']}/view.php?id={$image_id}";
            $image_url = "http://{$_SERVER['HTTP_HOST']}/onlineImages/view.php?id={$image_id}";

            //$delete_url = "http://{$_SERVER['HTTP_HOST']}/delete.php?token={$delete_token}";
            $delete_url = "http://{$_SERVER['HTTP_HOST']}/onlineImages/delete.php?token={$delete_token}";

            // 成功レスポンスを返す
            echo json_encode(['success' => true, 'image_url' => $image_url, 'delete_url' => $delete_url]);
        } else {
            // ファイル移動に失敗した場合
            echo json_encode(['success' => false, 'error' => 'ファイルの移動に失敗しました']);
            exit;
        }
    } else {
        // 許可されていないファイル形式の場合
        echo json_encode(['success' => false, 'error' => '許可されていないファイル形式です']);
        exit;
    }
} else {
    // ファイルのアップロードに失敗した場合
    echo json_encode(['success' => false, 'error' => 'ファイルのアップロードに失敗しました']);
    exit;
}
