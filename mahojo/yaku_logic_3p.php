<?php

require_once __DIR__ . '/yaku_logic_4p.php';

/*
|--------------------------------------------------------------------------
| 三麻シャンテン
|--------------------------------------------------------------------------
*/
function role_shanten_3p($tiles) {

    // 今は四麻ロジック流用
    return role_shanten_4p($tiles);
}

/*
|--------------------------------------------------------------------------
| 三麻AI分析
|--------------------------------------------------------------------------
*/
function analyze_hand_3p_candidates($tiles) {

    $roles = role_shanten_3p($tiles);

    $result = [
        'remain3' => [],
        'remain5' => []
    ];

    foreach ($roles as $role => $rem) {

        if ($rem == 3) {
            $result['remain3'][] = $role;
        }

        if ($rem == 5) {
            $result['remain5'][] = $role;
        }
    }

    return $result;
}