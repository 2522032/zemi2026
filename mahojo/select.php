<?php

declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/ensure_login.php';
require_once __DIR__ . '/connect_db.php';

/*
|--------------------------------------------------------------------------
| 牌画像
|--------------------------------------------------------------------------
*/
$tiles = [

    // 萬子
    '1m','2m','3m','4m','5m','6m','7m','8m','9m',

    // 筒子
    '1p','2p','3p','4p','5p','6p','7p','8p','9p',

    // 索子
    '1s','2s','3s','4s','5s','6s','7s','8s','9s',

    // 字牌
    'east','south','west','north',
    'white','green','red'
];
?>

<!DOCTYPE html>
<html lang="ja">

<head>
<meta charset="UTF-8">
<title>麻雀 手牌選択</title>

<style>

body{
    font-family:sans-serif;
    padding:20px;
    background:#f4f4f4;
}

h2{
    margin-bottom:15px;
}

.tile-area{
    display:flex;
    flex-wrap:wrap;
    gap:8px;
    margin-bottom:30px;
}

.tile{
    width:50px;
    height:70px;

    border:1px solid #333;
    border-radius:8px;

    cursor:pointer;

    background:white;

    display:flex;
    justify-content:center;
    align-items:center;

    overflow:hidden;
}

.tile:hover{
    transform:scale(1.05);
}

.tile img{
    width:100%;
    height:100%;
    object-fit:contain;
}

#hand{
    min-height:90px;

    padding:10px;

    border:2px dashed #999;
    border-radius:10px;

    display:flex;
    flex-wrap:wrap;
    gap:8px;

    background:white;
}

button{
    padding:10px 20px;
    font-size:16px;
    cursor:pointer;
}

select{
    padding:8px;
    font-size:16px;
}

.tile-area{
    display:flex;
    flex-wrap:wrap;
    gap:20px;
}

.tile-group{
    display:flex;
    align-items:center;
    gap:8px;
    flex-wrap:wrap;
}

.tile-group h3{
    margin-right:10px;
}

</style>
</head>

<body>

<h2>牌を選択 🀄</h2>
<div class="tile-area">

    <!-- 萬子 -->
    <div class="tile-group">

        <h3>萬子</h3>

        <?php for($i=1; $i<=9; $i++): ?>

            <img
                src="images/<?= $i ?>m.png"
                class="tile"
                onclick="addTile('<?= $i ?>m')"
            >

        <?php endfor; ?>

    </div>

    <!-- 筒子 -->
    <div class="tile-group">

        <h3>筒子</h3>

        <?php for($i=1; $i<=9; $i++): ?>

            <img
                src="images/<?= $i ?>p.png"
                class="tile"
                onclick="addTile('<?= $i ?>p')"
            >

        <?php endfor; ?>

    </div>

    <!-- ここで改行 -->
    <div style="width:100%"></div>

    <!-- 索子 -->
    <div class="tile-group">

        <h3>索子</h3>

        <?php for($i=1; $i<=9; $i++): ?>

            <img
                src="images/<?= $i ?>s.png"
                class="tile"
                onclick="addTile('<?= $i ?>s')"
            >

        <?php endfor; ?>

    </div>

    <!-- 字牌 -->
    <div class="tile-group">

        <h3>字牌</h3>

        <?php
        $jihai = [
            "east",
            "south",
            "west",
            "north",
            "white",
            "green",
            "red"
        ];

        foreach($jihai as $tile):
        ?>

            <img
                src="images/<?= $tile ?>.png"
                class="tile"
                onclick="addTile('<?= $tile ?>')"
            >

        <?php endforeach; ?>

    </div>

</div>
<h2>手牌</h2>

<div id="hand"></div>

<br>

<form
    action="analyze_and_save.php"
    method="POST"
>

    <input
        type="hidden"
        name="tiles"
        id="tilesInput"
    >

    <select name="mode">

        <option value="4p">
            四人麻雀
        </option>

        <option value="3p">
            三人麻雀
        </option>

    </select>

    <br><br>

    <button
        type="submit"
        onclick="return validateHand()"
    >
        AI判定
    </button>

</form>

<script>

const hand = [];

/*
|--------------------------------------------------------------------------
| 牌追加
|--------------------------------------------------------------------------
*/
function addTile(tile){

    if(hand.length >= 14){

        alert("14枚までです");
        return;
    }

    hand.push(tile);

    renderHand();
}

/*
|--------------------------------------------------------------------------
| 牌削除
|--------------------------------------------------------------------------
*/
function removeTile(index){

    hand.splice(index,1);

    renderHand();
}

/*
|--------------------------------------------------------------------------
| 手牌描画
|--------------------------------------------------------------------------
*/
function renderHand(){

    const handDiv = document.getElementById("hand");

    handDiv.innerHTML = "";

    hand.forEach((tile,index)=>{

        const div = document.createElement("div");

        div.className = "tile";

        div.innerHTML = `
            <img src="images/${tile}.png">
        `;

        div.onclick = () => removeTile(index);

        handDiv.appendChild(div);
    });

    document.getElementById("tilesInput").value =
        hand.join(",");
}

/*
|--------------------------------------------------------------------------
| 送信チェック
|--------------------------------------------------------------------------
*/
function validateHand(){

    if(hand.length !== 14){

        alert("14枚選択してください");

        return false;
    }

    return true;
}

</script>

</body>
</html>