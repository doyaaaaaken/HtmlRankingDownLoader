<?php
/**
* 1週間おきの膨大な過去の日付を生成する用スクリプト
* (file_loaderの$dateListに代入する)
*/
$date = "2011-12-05";
$str = "";

for($i=0; $i<300; $i++){
	$date = date("Y-m-d", strtotime($date." - 7 day"));
	$str .= "\"".$date."\", ";
}

echo $str;