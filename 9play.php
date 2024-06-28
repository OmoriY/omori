<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8"/>
<script type="text/javascript" src="zepto.min.js"></script>
<script type="text/javascript">
var xs = {x:123456789, y:362436069, z:521288629, w:88675123};
function myrand_init(seed){
 xs.x = 123456789; // xs['x'] = 123456789; のように書くこともできる
 xs.y = 362436069;
 xs.z = 521288629;
 xs.w = (seed == undefined) ? 88675123 : seed;
}
function myrand(){
 var t;
 t = (xs.x ^ (xs.x << 11));
 xs.x = xs.y;
 xs.y = xs.z;
 xs.z = xs.w;
 xs.w = (xs.w ^ (xs.w >>> 19)) ^ (t ^ (t >>> 8));
 return (xs.w >>> 0) / 4294967296; // 「>>> 0」を付けると符号なし整数に変換できる
}
</script>
</head>
<body onload="play(0)">
<h2>レースゲーム(PLAY BACK)</h2>
<p>
 <li>これは過去のプレイを再生します。</li>
 <li>下の「戻る」から戻ることが出来ます。</li>
 <li>ぜひ他の人の走りを参考にしてください。</li>
 <li>ランキングから他の人のプレイが見れます。</li>
</p>
<font size="+2">
タイム：<span id="time" style="display:inline-block;width:68px;color:green"></span>
スピード：<span id="speed" style="display:inline-block;width:44px;color:red;"></span>km/h&ensp;
残り：<span id="rest" style="display:inline-block;width:64px;color:blue;"></span>km
</font>
<table border=1 cellspacing=0><tr><td>
<canvas id="c1" width="500px" height="500px" style="display: block;"></canvas>
</td></tr></table>

<script type="text/javascript">
var canvas = document.getElementById('c1');
var ctx = canvas.getContext("2d");
var i;
var mycar = new Image(); mycar.src = "mycar.png"; // 自機の画像
var car1 = new Image(); car1.src = "car1.png"; // 敵機の画像
var car2 = new Image(); car2.src = "car2.png"; // 敵機の画像
var mycarL = new Image(); mycarL.src = "myL.png"; // 自機の画像
var mycarR = new Image(); mycarR.src = "myR.png"; // 自機の画像
var jx, jy, jspeed; // 自機の情報
var enemy; // 敵機の配列
var rest; // 残りの距離
var key; // キー入力配列
var flag; // 衝突判定フラグ
var elapsed; // 経過時間
var start; // ゲーム開始時刻
var tick; // ゲーム開始後の刻み数 update() が実行される毎に +1 される
var rec={
<?php 
 $r = $_GET['r']; 
$file = file("9.txt"); 
 foreach($file as $f){ 
 list($date, $name, $score, $rec_key, $rec) = explode(',', rtrim($f)); 
 if($score == $r) break; 
 } 
 $rec_key = explode("/", $rec_key); 
 $rec = explode("/", $rec); 
 for($i = 0; $i < count($rec_key); $i++){ 
 echo "$rec_key[$i]:$rec[$i]"; 
 if( $i < count($rec_key) - 1 ) echo ","; 
 } 
?>
}; // 動作記録用の連想配列
var playback; // 1 だと再生モード
var draw_start_screen; // 1 だとスタート画面の描画のみ
function play(mode){
 jx = 234;
 jy = 360;
 jspeed = 0; // 自機の情報
 enemy = []; // 敵機の配列(最初は空)
 rest = 50000; // 残りの距離
 key = []; // キー入力状態の初期化
 flag = 0; // 衝突判定フラグ
 start = new Date().getTime();
 tick = 0;
 draw_start_screen = playback = 0;
 if( mode==0 ){
 draw_start_screen = 1;
 }else if( mode==1 ){
 rec = {}; //キー操作記録用の連想配列を初期化
 }else if( mode==2 ){
 playback = 1;
 }
 myrand_init();
 update();
}

function update(){
 if( flag == 1 ){
 // 衝突時のメッセージを表示する
 ctx.fillStyle = '#000000'; // 文字の色
ctx.font = "60px 'ＭＳ ゴシック'";
ctx.textAlign="center";
ctx.textBaseline="bottom";
ctx.fillText('衝突しました', 250, 250);
 return;
 }
 if(playback==1){
 if( rec[tick] & 1 ) jx = Math.max(-2, jx - 4); // ←が押されている
 if( rec[tick] & 2 ) jspeed = Math.min(100, jspeed + 1); // ↑が押されている
 if( rec[tick] & 4 ) jx = Math.min(470, jx + 4); // →が押されている
 if( rec[tick] & 8 ) jspeed = Math.max(-1, jspeed - 2); // ↓が押されている
}
tick++;
elapsed = parseInt((new Date().getTime() - start) / 10) / 100;
document.getElementById("speed").innerHTML = jspeed;
document.getElementById("time").innerHTML = elapsed;
document.getElementById("rest").innerHTML = parseInt(rest / 100);
 rest -= jspeed; //残りの距離を更新
 if( rest <= 0 ){
 alert("再生が終了しました。"); 
 return;
 }
 ctx.fillStyle = '#a0a0a0'; // 背景(道路の色)を指定
 ctx.fillRect(0, 0, 500, 500); // 背景色で塗る
 ctx.fillStyle = '#ffffff';
 ctx.fillRect(245, (50000 - rest) % 750 - 250, 10, 250);
 if( rest > 49850 ){
  ctx.fillStyle = '#ffffff'; // 文字の色
  ctx.font = "40px 'ＭＳ ゴシック'";
  ctx.textAlign = "left";
  ctx.textBaseline = "bottom";
  ctx.fillText('START', 0, 50350 - rest);
  ctx.fillRect(0, 50350 - rest, 500, 10);
 }
 ctx.fillText('GOAL', 0, 420 - rest);
 ctx.fillRect(0, 420 - rest, 500, 10);
if( rec[tick] & 1 ) {
  ctx.drawImage(mycarL, jx, jy);
 }else if( rec[tick] & 4 ) {
  ctx.drawImage(mycarR, jx, jy);
 }else{
  ctx.drawImage(mycar, jx, jy); // 自機を描画
 }
 for(i = 0; i < enemy.length; i++){ // 敵機を動かす
 if( jx > enemy[i].x ) enemy[i].x += (myrand() - 0.3 ) * 3;
 else enemy[i].x -= (myrand() - 0.3 ) * 3;
 enemy[i].y += (jspeed - enemy[i].speed) / 10;
 if( enemy[i].y <= -100 || enemy[i].y >= 2000 ){ // 敵機が遠くに離れた
 enemy.splice(i, 1); // 敵機を配列から削除する
 i--; // ループ変数を一つ減らす
 }
 }
 if( myrand() < 0.001 * jspeed + 0.01 ) { // 敵機をランダムに出現させる
 var kind;
 if( myrand() < 0.5 ){ 
  kind = car1; 
 }else{ 
  kind = car2; 
 } 
 // 敵機の種類
 enemy.push({kind:kind, x:myrand() * 470, y:-32, size:16, speed:myrand() * 50}); // 配列に要素を追加
 //kind:敵機の種類 x:X 座標 y:Y 座標 size:半分のサイズ speed:スピード
 }
 if( myrand() < 0.01 ) { // 後方からも敵機をランダムに出現させる
  enemy.push({kind:car1, x:myrand() * 470, y:500, size:16, speed:myrand() * 50});
 }
 for(i = 0; i < enemy.length; i++){ // 敵機を描画
 ctx.drawImage(enemy[i].kind, enemy[i].x, enemy[i].y);
 if( (jx - enemy[i].x) * (jx - enemy[i].x) + (jy - enemy[i].y) * (jy - enemy[i].y)
 < (14 + enemy[i].size) * (14 + enemy[i].size) ) flag = 1; // 衝突判定
 }
 if( draw_start_screen ) return; //スタート画面を描画して終了
 setTimeout("update()", 20); // 20 ミリ秒経過後に update() を実行する
}
window.addEventListener ('keydown', function(e){key[e.keyCode] = true;}); //キーが押された
window.addEventListener ('keyup', function(e){key[e.keyCode] = false;}); //キーが離された
</script>
<input type="button" value=" 再生する " onclick="play(2)"><a href="9.html" value="戻る">戻る</a><br><br>
<script type="text/javascript">
function ranking(){
 _d = new Date().getTime(); //キャッシュ回避のため日時を利用する
 $.get("9ranking.php?_d=" + _d, function(data){
 var a = data.split("\n"); //改行で区切る
 var table = "<table border=1 cellspacing=0 cellpadding=2>";
 table += "<tr><td>順位</td><td>時間</td><td>名前</td><td>日時</td></tr>";
 for(i=0;i<a.length-1;i++){
 var b = a[i].split(","); //カンマで区切る
 table += '<tr><td><a href="9play.php?r=' + b[2] + '">' + (i+1) + "</a></td><td>" + b[2] +
"</td><td>"
 + b[1] + "</td><td>" + b[0] + "</td></tr>";
 }
 table += "</table>";
 document.getElementById("ranking").innerHTML = table;
});
}
</script>
<b>ランキング</b><br>
<div id="ranking"></div>
<script type="text/javascript">
 ranking();
</script>
</body>
</html>
