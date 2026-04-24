<?php
// yaku_logic/yaku_logic_4p.php
require_once __DIR__.'/yaku_logic_common.php';

function analyze_hand_4p($tiles) {
    $table = count_tiles($tiles);

    $result = [
        'possible_yaku' => [],
        'shanten' => null,
        'info' => [],
    ];

    // 四麻用役判定
    if (is_tanyao_4p($table)) {
        $result['possible_yaku'][] = 'タンヤオ（四麻）';
    }
    if ($yaku = is_yakuhai_4p($table)) {
        $result['possible_yaku'][] = $yaku;
    }
    if (is_chinitsu_4p($table)) {
        $result['possible_yaku'][] = '清一色（四麻）';
    }

    $result['info']['hand_string'] = hand_to_string($tiles);
    $result['shanten'] = calc_shanten_4p($table);

    return $result;
}

// ------- 四麻用役判定（例） -------
function is_tanyao_4p($table) {
    foreach ($table as $tile => $c) {
        if (preg_match('/(1|9)[mps]|east|south|west|north|white|green|red/', $tile)) {
            return false;
        }
    }
    return true;
}
function is_chinitsu_4p($table) {
    $types = [];
    foreach ($table as $k => $v) {
        if (preg_match('/[mps]$/', $k, $m)) {
            $types[$m[0]] = true;
        } else {
            return false;
        }
    }
    return count($types) === 1;
}
function is_yakuhai_4p($table) {
    $yakuhai_list = [
        'east' => '東（四麻）',
        'south' => '南（四麻）',
        'west' => '西（四麻）',
        'north' => '北（四麻）',
        'white' => '白（四麻）',
        'green' => '發（四麻）',
        'red' => '中（四麻）'
    ];
    foreach ($yakuhai_list as $k => $yaku_name) {
        if (isset($table[$k]) && $table[$k] >= 3) {
            return "役牌 ($yaku_name)";
        }
    }
    return false;
}
function calc_shanten_4p($table) {
    // 仮の簡易判定（実装拡張可）
    return 4;
}
?>