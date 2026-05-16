<?php
declare(strict_types=1);

session_start();

ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/connect_db.php';

$pdo->exec("SET search_path TO suggest_plan");

/*
|--------------------------------------------------------------------------
| hand_id
|--------------------------------------------------------------------------
*/
$handId = (int)($_GET['hand_id'] ?? 0);

if ($handId <= 0) {
    exit('hand_idがありません');
}

/*
|--------------------------------------------------------------------------
| データ取得
|--------------------------------------------------------------------------
*/
$sql = "
    SELECT
        h.tiles,
        p.predicted_yaku,
        p.ai_comment
    FROM hands h
    JOIN predictions p
        ON h.id = p.hand_id
    WHERE h.id = :hand_id
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':hand_id' => $handId
]);

$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    exit('データが見つかりません');
}

/*
|--------------------------------------------------------------------------
| 手牌
|--------------------------------------------------------------------------
*/
$tiles = json_decode($data['tiles'], true);
?>

<!DOCTYPE html>
<html lang="ja">

<head>

<meta charset="UTF-8">

<title>AI判定結果</title>

<style>

body{
    font-family:sans-serif;
    padding:20px;
    background:#f4f4f4;
}

.tile{
    display:inline-block;

    width:50px;
    height:70px;

    line-height:70px;
    text-align:center;

    border:1px solid #333;
    border-radius:8px;

    background:white;

    margin:4px;
}

.box{
    background:white;

    padding:20px;

    border-radius:10px;

    margin-top:20px;
}

</style>

</head>

<body>

<h1>AI判定結果 🀄</h1>

<div class="box">

<h2>手牌</h2>

<?php foreach($tiles as $tile): ?>

    <div class="tile">
        <?= htmlspecialchars($tile, ENT_QUOTES, 'UTF-8') ?>
    </div>

<?php endforeach; ?>

</div>

<div class="box">

<h2>予想役</h2>

<p>
    <?= htmlspecialchars($data['predicted_yaku']) ?>
</p>

</div>

<div class="box">

<h2>AIコメント</h2>

<pre><?= htmlspecialchars($data['ai_comment']) ?></pre>

</div>

</body>
</html>