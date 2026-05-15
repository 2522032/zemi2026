<?php
require_once __DIR__.'/yaku_list.php';
function role_shanten_3p($tiles) {
    // 三麻独自の処理やツモ次第でカスタマイズする場合はこちらへ
    return role_shanten_4p($tiles);
}
function analyze_hand_3p_candidates($tiles) {
    $roles = role_shanten_3p($tiles);
    $result = ['remain3'=>[], 'remain5'=>[]];
    foreach ($roles as $role=>$rem) {
        if ($rem == 3) $result['remain3'][] = $role;
        if ($rem == 5) $result['remain5'][] = $role;
    }
    return $result;
}