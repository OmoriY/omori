<?php 
$rec_key = join("/",$_POST['rec_key']); //要素間を / で繋げる 
$rec = join("/",$_POST['rec']); //要素間を / で繋げる 
$fw = fopen("9.txt", "a"); // 追記する 
fwrite( $fw, date("y/m/d H:i:s").",".$_POST['name'].",".$_POST['score'].","); 
fwrite($fw, "$rec_key,$rec\n"); 
fclose($fw); 
?>
