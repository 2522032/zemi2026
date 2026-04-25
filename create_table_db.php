<?php
require_once __DIR__ . '/connect_db.php';

try {
    $pdo->beginTransaction();

    // users
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id SERIAL PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // hands
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS hands (
            id SERIAL PRIMARY KEY,
            user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
            tiles JSONB NOT NULL,
            mode VARCHAR(2) NOT NULL, -- '3p' or '4p'
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // predictions
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS predictions (
            id SERIAL PRIMARY KEY,
            hand_id INTEGER REFERENCES hands(id) ON DELETE CASCADE,
            result JSONB NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // インデックス（検索高速化）
    $pdo->exec("
        CREATE INDEX IF NOT EXISTS idx_hands_user
        ON hands(user_id);
    ");

    $pdo->exec("
        CREATE INDEX IF NOT EXISTS idx_predictions_hand
        ON predictions(hand_id);
    ");

    $pdo->commit();
    echo "OK: テーブル作成完了\n";

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "ERROR: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "\n";
}