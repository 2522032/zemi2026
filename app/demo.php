<?php
require_once __DIR__.'/data.php';
require_once __DIR__.'/yaku_logic_4p.php';
require_once __DIR__.'/yaku_logic_3p.php';
require_once __DIR__.'/yaku_list.php';

function show_candidates($tiles, $mode, $title = "") {
    global $yaku_list;
    if ($mode === '3p') {
        $result = analyze_hand_3p_candidates($tiles);
    } else {
        $result = analyze_hand_4p_candidates($tiles);
    }
    echo ($title ? $title."\n" : "");
    echo "牌配列: " . implode(", ", $tiles) . "\n";

    if (count($result['remain3'])) {
        echo "あと3手で狙える役：\n";
        foreach ($result['remain3'] as $role) {
            $desc = $yaku_list[$role] ?? "";
            echo "・$role: $desc\n";
        }
    } else {
        echo "あと3手で狙える役：なし\n";
    }
    if (count($result['remain5'])) {
        echo "あと5手で狙える役：\n";
        foreach ($result['remain5'] as $role) {
            $desc = $yaku_list[$role] ?? "";
            echo "・$role: $desc\n";
        }
    } else {
        echo "あと5手で狙える役：なし\n";
    }
    echo "\n";
}

if (php_sapi_name() !== 'cli') echo "<pre>";
show_candidates($GLOBALS['tiles_4p'], '4p', '【四人麻雀の例】');
show_candidates($GLOBALS['tiles_3p'], '3p', '【三人麻雀の例】');
if (php_sapi_name() !== 'cli') echo "</pre>";