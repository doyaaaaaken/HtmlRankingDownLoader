<?php
//PHP Simple HTML DOM Parser Manualの読み込み
require_once 'lib/simple_html_dom.php';

//対象URL指定
$html = file_get_html('http://www.oricon.co.jp/rank/obb/w/2014-12-01/p/2/');

$rank = 1;
$date = "2014-12-01";
$WeeklyBookDatas = array();

//DOMをパースし必要なデータを変数に代入
if($html != ""){
	foreach($html->find('div[class=inner] div[class=wrap-text]') as $book){
		
		$title = $book->find('h2', 0)->plaintext;
		$author = $book->find('p', 0)->plaintext;
		foreach($book->find('ul', 0)->find('li') as $elem){
			$elems[] = $elem->plaintext;
		}

		array_push($WeeklyBookDatas ,new WeeklyBookData(
			$date, $rank++, $title, $author, $elems[0], $elems[1], $elems[2], $elems[3]));
	}
	$info_msg = "File exist!!";

}else {
	$info_msg = "Failure!!  Can't get file!!";
}

//データ＆結果メッセージ出力
var_dump($WeeklyBookDatas);
echo $info_msg;



/**
* 1つの週次本データを表すオブジェクト
*/
class WeeklyBookData{
	private $date;//ランキング週
	private $rank;//ランキング
	private $title;//タイトル
	private $author;//著者
	private $company;//出版社
	private $publishDate;//発売日
	private $price;//価格
	private $publishNum;//推定売上部数

	public function __construct($date, $rank, $title, $author, $company, $publishDate, $price, $publishNum){
		$this->date = $date;
		$this->rank = $rank;
		$this->title = $title;
		$this->author = $author;
		$this->company = $company;
		$this->publishDate = $publishDate;
		$this->price = $price;
		$this->publishNum = $publishNum;
	}
}