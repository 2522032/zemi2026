<?php
// app/yaku_logic.php

/**
 *  analyze_hand($tiles, $num_players)
 *  牌配列, 人数(3 or 4)でロジック振り分け
 *  return: 役配列・シャンテン数・info配列
 */
function analyze_hand($tiles, $num_players = 4) {
    if ($num_players === 3) {
        require_once __DIR__.'/yaku_logic/yaku_logic_3p.php';
        return analyze_hand_3p($tiles);
    } else {
        require_once __DIR__.'/yaku_logic/yaku_logic_4p.php';
        return analyze_hand_4p($tiles);
    }
}
?>