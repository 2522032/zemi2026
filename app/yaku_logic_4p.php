<?php
require_once __DIR__.'/yaku_list.php';

// --------- 補助関数 ---------
function count_suits($tiles) {
    $cnt = ['m'=>0,'p'=>0,'s'=>0,'z'=>0];
    foreach ($tiles as $t) {
        if (strpos($t,'m')) $cnt['m'] += substr($t,-1)=='m'?1:0;
        if (strpos($t,'p')) $cnt['p'] += substr($t,-1)=='p'?1:0;
        if (strpos($t,'s')) $cnt['s'] += substr($t,-1)=='s'?1:0;
        if (!in_array(substr($t,-1), ['m','p','s'])) $cnt['z']++;
    }
    return $cnt;
}
// 正確なシャンテン調整には本格的なアルゴリズムが必要ですが、枠組み重視で下記例を提示
function normal_shanten($tiles) {
    // 面子/対子を数える簡易実装。「メンツ4＋頭1」、14枚想定。
    $cnts = array_count_values($tiles);
    $mentsu = 0; $pair = 0;
    foreach ($cnts as $t=>$v) {
        if ($v >= 3) $mentsu += intdiv($v,3);
        if ($v >= 2) $pair += 1;
    }
    $shanten = max(0, 4-$mentsu);
    if ($pair >= 1) $shanten--;
    return max(0, $shanten);
}
function chiitoitsu_shanten($tiles) {
    $cnt = array_count_values($tiles);
    $pair = 0;
    foreach($cnt as $c) if ($c >= 2) $pair++;
    return max(0, 7 - $pair);
}
function kokushi_shanten($tiles) {
    $kokushi_needed = [
        "1m","9m","1p","9p","1s","9s",
        "east","south","west","north","white","green","red"
    ];
    $unique = 0; $pair = false;
    $cnts = array_count_values($tiles);
    foreach ($kokushi_needed as $h) {
        if (($cnts[$h]??0) >= 1) $unique++;
        if (($cnts[$h]??0) >= 2) $pair=true;
    }
    $shanten = 13 - $unique;
    if (!$pair) $shanten++;
    return $shanten;
}
function chinitsu_shanten($tiles) {
    $cnt = count_suits($tiles);
    $max = max($cnt['m'],$cnt['p'],$cnt['s']);
    return count($tiles) - $max;
}
function honitsu_shanten($tiles) {
    $cnt = count_suits($tiles);
    $max = max($cnt['m'],$cnt['p'],$cnt['s']);
    return count($tiles) - ($max+$cnt['z']);
}
function toitoi_shanten($tiles) {
    $cnts = array_count_values($tiles);
    $kotsu = 0;
    foreach ($cnts as $v) if ($v >= 3) $kotsu += intdiv($v,3);
    return max(0, 4-$kotsu);
}
function honroutou_shanten($tiles) {
    foreach ($tiles as $t) {
        if (preg_match('/[2-8][mps]/', $t)) return 99;
    }
    return normal_shanten($tiles);
}
function tsuuiisou_shanten($tiles) {
    foreach ($tiles as $t) {
        if (!in_array($t, ["east","south","west","north","white","green","red"])) return 99;
    }
    return normal_shanten($tiles);
}
function tanyao_shanten($tiles) {
    foreach ($tiles as $t) {
        if (preg_match('/[1|9][mps]/', $t) || in_array($t,["east","south","west","north","white","green","red"])) return 99;
    }
    return normal_shanten($tiles);
}
// 各役のシャンテン関数（サンプル/拡張可）
function role_shanten_4p($tiles) {
    // 役名 => シャンテン関数
    $r = [];
    $r['立直']          = normal_shanten($tiles);
    $r['門前清自摸和'] = normal_shanten($tiles);
    $r['平和']         = normal_shanten($tiles);
    $r['断么九']        = tanyao_shanten($tiles);
    $r['一盃口']        = normal_shanten($tiles);
    $r['二盃口']        = normal_shanten($tiles);
    $r['三色同順']      = normal_shanten($tiles);
    $r['三色同刻']      = normal_shanten($tiles);
    $r['一気通貫']      = normal_shanten($tiles);
    $r['対々和']        = toitoi_shanten($tiles);
    $r['三暗刻']        = normal_shanten($tiles);
    $r['三槓子']        = normal_shanten($tiles);
    $r['小三元']        = normal_shanten($tiles);
    $r['混老頭']        = honroutou_shanten($tiles);
    $r['混一色']        = honitsu_shanten($tiles);
    $r['清一色']        = chinitsu_shanten($tiles);
    $r['混全帯么九']    = normal_shanten($tiles);
    $r['純全帯么九']    = normal_shanten($tiles);
    $r['字一色']        = tsuuiisou_shanten($tiles);
    $r['緑一色']        = normal_shanten($tiles);
    $r['七対子']        = chiitoitsu_shanten($tiles);
    $r['国士無双']      = kokushi_shanten($tiles);
    $r['四暗刻']        = normal_shanten($tiles);
    $r['大三元']        = normal_shanten($tiles);
    $r['小四喜']        = normal_shanten($tiles);
    $r['大四喜']        = normal_shanten($tiles);
    $r['四槓子']        = normal_shanten($tiles);

    return $r;
}
function analyze_hand_4p_candidates($tiles) {
    $roles = role_shanten_4p($tiles);
    $result = ['remain3' => [], 'remain5' => []];
    foreach ($roles as $role => $rem) {
        if ($rem == 3) $result['remain3'][] = $role;
        if ($rem == 5) $result['remain5'][] = $role;
    }
    return $result;
}