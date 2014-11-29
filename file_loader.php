<?php
require_once 'lib/simple_html_dom.php';

$html = file_get_html('http://www.oricon.co.jp/rank/obb/w/2014-12-01/');//対象URL指定

if($html != ""){
	$element = $html->find('a');
	foreach($element as $elem){
		echo htmlspecialchars($elem);
	}
	$info_msg = "ファイルの取得に成功しました";
}else {
	$info_msg = "ファイルの取得に成功しました";
}

echo $info_msg;//結果メッセージ出力