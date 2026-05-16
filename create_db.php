<?php
declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/connect_db.php';

$pdo->exec("CREATE SCHEMA IF NOT EXISTS suggest_plan");
$pdo->exec("SET search_path TO suggest_plan");

header('Content-Type: text/plain; charset=utf-8');

try {

    $pdo->beginTransaction();

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id SERIAL PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT NOW()
        );
    ");

  
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS hands (
            id SERIAL PRIMARY KEY,

            user_id INT NOT NULL,

            -- 手牌
            tiles TEXT NOT NULL,

            created_at TIMESTAMP NOT NULL DEFAULT NOW(),

            CONSTRAINT fk_hands_user
                FOREIGN KEY (user_id)
                REFERENCES users(id)
                ON DELETE CASCADE
        );
    ");

 
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS predictions (
            id SERIAL PRIMARY KEY,

            hand_id INT NOT NULL,

            -- AI予想役
            predicted_yaku VARCHAR(100) NOT NULL,

            -- 点数
            score INT,

            -- AIコメント
            ai_comment TEXT,

            -- AIおすすめ打牌
            recommended_tile VARCHAR(20),

            created_at TIMESTAMP NOT NULL DEFAULT NOW(),

            CONSTRAINT fk_predictions_hand
                FOREIGN KEY (hand_id)
                REFERENCES hands(id)
                ON DELETE CASCADE
        );
    ");

    $pdo->commit();

    echo "DB・テーブル作成完了";

} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo "エラー: " . $e->getMessage();
}