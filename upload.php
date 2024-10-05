<?php
// エラー表示設定
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$target_dir = "uploads/";
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];

// データベース接続
$mysqli = new mysqli("127.0.0.1", "root", "password", "image_hosting", 3307);

if ($mysqli->connect_error) {
    die(json_encode(['success' => false, 'error' => 'データベース接続エラー: ' . $mysqli->connect_error]));
}

if ($_FILES['image']['error'] == 0) {
    $file_name = basename($_FILES['image']['name']);
    $target_file = $target_dir . uniqid() . '-' . $file_name;
    $file_type = mime_content_type($_FILES['image']['tmp_name']);

    if (in_array($file_type, $allowed_types)) {
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $delete_token = bin2hex(random_bytes(16));
            $stmt = $mysqli->prepare("INSERT INTO images (image_name, image_path, delete_token) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $file_name, $target_file, $delete_token);
            $stmt->execute();
            $image_id = $stmt->insert_id;

            $image_url = "http://{$_SERVER['HTTP_HOST']}/view.php?id={$image_id}";
            $delete_url = "http://{$_SERVER['HTTP_HOST']}/delete.php?token={$delete_token}";

            echo json_encode(['success' => true, 'image_url' => $image_url, 'delete_url' => $delete_url]);
        } else {
            echo json_encode(['success' => false, 'error' => 'ファイルの移動に失敗しました']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => '許可されていないファイル形式です']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'ファイルのアップロードに失敗しました']);
}
