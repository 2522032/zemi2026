<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$result = null;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["image"])) {

    // 一時ファイル
    $file_tmp = $_FILES["image"]["tmp_name"];

    // Flask API（正しい書き方）
    $url = "https://zemi2026-1.onrender.com/predict";

    // ★これ必須
    $post_data = [
        "image" => new CURLFile($file_tmp)
    ];

    // cURL
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "ngrok-skip-browser-warning: true"
    ]);

    // 実行（1回だけ）
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo "cURL Error: " . curl_error($ch);
    }

    // JSON → 配列
    $result = json_decode($response, true);
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>麻雀AI 判定</title>
</head>
<body>

<h2>牌画像アップロード</h2>

<form method="POST" enctype="multipart/form-data">
    <input type="file" name="image" accept="image/*" required>
    <button type="submit">判定する</button>
</form>

<?php if ($result): ?>
    <h3>判定結果</h3>
    <p>牌：<?= htmlspecialchars($result["result"]) ?></p>
    <p>信頼度：<?= round($result["confidence"], 3) ?></p>
<?php endif; ?>

</body>
</html>