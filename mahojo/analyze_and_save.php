<?php
declare(strict_types=1);

session_start();

ini_set('display_errors', '1');
error_reporting(E_ALL);

/*
|--------------------------------------------------------------------------
| 必要ファイル
|--------------------------------------------------------------------------
*/
require_once __DIR__ . '/connect_db.php';
require_once __DIR__ . '/yaku_logic_4p.php';
require_once __DIR__ . '/yaku_logic_3p.php';
require_once __DIR__ . '/yaku_list.php';

$pdo->exec("SET search_path TO suggest_plan");

/*
|--------------------------------------------------------------------------
| ログイン確認
|--------------------------------------------------------------------------
*/
$userId = $_SESSION['user_id'] ?? 0;

if (!$userId) {
    exit('ログインしてください');
}

/*
|--------------------------------------------------------------------------
| POST受け取り
|--------------------------------------------------------------------------
*/
$tilesString = $_POST['tiles'] ?? '';
$mode = $_POST['mode'] ?? '4p';

if ($tilesString === '') {
    exit('手牌がありません');
}


$tiles = explode(',', $tilesString);

if ($mode === '3p') {

    $result = analyze_hand_3p_candidates($tiles);
    $label = '三人麻雀';

} else {

    $result = analyze_hand_4p_candidates($tiles);
    $label = '四人麻雀';
}

/*
|--------------------------------------------------------------------------
| 推奨役
|--------------------------------------------------------------------------
*/
$allRoles = [];

if ($mode === '3p') {
    $allRoles = role_shanten_3p($tiles);
} else {
    $allRoles = role_shanten_4p($tiles);
}

/*
|--------------------------------------------------------------------------
| 最小シャンテン役を取得
|--------------------------------------------------------------------------
*/
asort($allRoles);

$targetYaku = array_key_first($allRoles);

$bestShanten = $allRoles[$targetYaku];

/*
|--------------------------------------------------------------------------
| コメント生成
|--------------------------------------------------------------------------
*/
$comment =
    "最も近い役: {$targetYaku}\n" .
    "シャンテン数: {$bestShanten}\n\n" .
    "あと3手候補:\n" .
    (implode(' / ', $result['remain3']) ?: 'なし') .
    "\n\nあと5手候補:\n" .
    (implode(' / ', $result['remain5']) ?: 'なし');

/*
|--------------------------------------------------------------------------
| DB保存
|--------------------------------------------------------------------------
*/
try {

    $pdo->beginTransaction();

    /*
    |--------------------------------------------------------------------------
    | hands 保存
    |--------------------------------------------------------------------------
    */
    $stmt = $pdo->prepare("
        INSERT INTO hands (
            user_id,
            tiles
        )
        VALUES (
            :user_id,
            :tiles
        )
        RETURNING id
    ");

    $stmt->execute([
        ':user_id' => $userId,
        ':tiles' => json_encode($tiles)
    ]);

    $handId = (int)$stmt->fetchColumn();

    /*
    |--------------------------------------------------------------------------
    | predictions 保存
    |--------------------------------------------------------------------------
    */
    $stmt = $pdo->prepare("
        INSERT INTO predictions (
            hand_id,
            predicted_yaku,
            ai_comment
        )
        VALUES (
            :hand_id,
            :predicted_yaku,
            :ai_comment
        )
    ");

    $stmt->execute([
        ':hand_id' => $handId,
        ':predicted_yaku' => $targetYaku,
        ':ai_comment' => $comment
    ]);

    $pdo->commit();

    /*
    |--------------------------------------------------------------------------
    | result.phpへ移動
    |--------------------------------------------------------------------------
    */
    header("Location: result.php?hand_id={$handId}");
    exit;

} catch (Throwable $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    exit('保存エラー: ' . $e->getMessage());
}