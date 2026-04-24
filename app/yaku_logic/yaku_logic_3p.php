<?php
// yaku_logic/yaku_logic_3p.php
require_once __DIR__.'/yaku_logic_common.php';

function analyze_hand_3p($tiles) {
    $table = count_tiles($tiles);

    $result = [
        'possible_yaku' => [],
        'shanten' => null,
        'info' => [],
    ];

    // 三麻独特の役判定例 (萬子なし etc.)
    if (is_tanyao_3p($table)) {
        $result['possible_yaku'][] = 'タンヤオ（三麻）';
    }
    if ($yaku = is_yakuhai_3p($table)) {
        $result['possible_yaku'][] = $yaku;
    }
    if (is_chinitsu_3p($table)) {
        $result['possible_yaku'][] = '清一色（三麻）';
    }
    if (is_kita_nuki($table)) {
        $result['possible_yaku'][] = '北抜き';
    }

    $result['info']['hand_string'] = hand_to_string($tiles);
    $result['shanten'] = calc_shanten_3p($table);

    return $result;
}

// ------- 三麻用役判定（例） -------

function is_tanyao_3p($table) {
    // 萬子なし、幺九牌（1,9,字牌）なし
    foreach ($table as $tile => $c) {
        if (preg_match('/1p|9p|1s|9s|east|south|west|white|green|red/', $tile)) {
            return false;
        }
        if (preg_match('/[m]/', $tile)) { // 萬子含み →不可
            return false;
        }
    }
    return true;
}
function is_chinitsu_3p($table) {
    $types = [];
    foreach ($table as $k => $v) {
        if (preg_match('/[ps]$/', $k, $m)) {
            $types[$m[0]] = true;
        } else {
            // 字牌含みは不可
            return false;
        }
    }
    return count($types) === 1;
}
function is_yakuhai_3p($table) {
    $yakuhai_list = [
        'east' => '東（三麻）',
        'south' => '南（三麻）',
        'west' => '西（三麻）',
        'white' => '白（三麻）',
        'green' => '發（三麻）',
        'red' => '中（三麻）',
        // 北は抜きなので役牌に含めない
    ];
    foreach ($yakuhai_list as $k => $yaku_name) {
        if (isset($table[$k]) && $table[$k] >= 3) {
            return "役牌 ($yaku_name)";
        }
    }
    return false;
}
function is_kita_nuki($table) {
    // 三麻北抜き判定(実際は抜き数管理必要)
    return isset($table['north']);
}
function calc_shanten_3p($table) {
    // 仮の簡易判定（実装拡張可）
    return 3;
}
?>