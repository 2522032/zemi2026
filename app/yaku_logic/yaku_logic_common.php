<?php
// yaku_logic/yaku_logic_common.php

/**
 * 牌配列をカウントして連想配列にする共通関数
 * 例: ["2m","3m","3m","east"] => ['2m'=>1, '3m'=>2, 'east'=>1]
 */
function count_tiles($tiles) {
    $table = [];
    foreach ($tiles as $t) {
        if (!isset($table[$t])) $table[$t] = 0;
        $table[$t]++;
    }
    return $table;
}

/**
 * 配列を文字列化（画面出力など用）
 */
function hand_to_string($tiles) {
    return implode(' ', $tiles);
}
?>