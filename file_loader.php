<?php
// header("Content-Type: text/html; charset=SHIFT-JIS");

//PHP Simple HTML DOM Parser Manualの読み込み
require_once 'lib/simple_html_dom.php';

//対象URL指定
define("URL_PRE", "http://www.oricon.co.jp/rank/obb/w/");
define("URL_MID", "/p/");
define("URL_SUF", "/");

//データ取得する週（何日付けのデータか？を記す）
$date = "2014-12-01";

//DOMをパースし必要なデータを変数に代入
$WeeklyBookDatas = getWeeklyBookDatas($date);

//CSVデータ出力＆結果メッセージ出力
outputCsvData($WeeklyBookDatas);
echo "******** Success!! **********";

///////////////////////////
///// 以下クラス・メソッド群 /////
///////////////////////////

//引数指定された日にち付けのURLの売上TOP30の週次データを取得する
function getWeeklyBookDatas($date){
	$WeeklyBookDatas = array();
	
	for ($pageNum=1; $pageNum<=3 ; $pageNum++) { //1ページにつき10タイトルしか載っていないため、3ページ見る必要がある
		$html = file_get_html(URL_PRE.$date.URL_MID.$pageNum.URL_SUF);

		if($html != ""){
			$rank = $pageNum * 10 - 9;

			foreach($html->find('div[class=inner] div[class=wrap-text]') as $book){

				$title = $book->find('h2', 0)->plaintext;
				$author = $book->find('p', 0)->plaintext;

				$elems = array();
				foreach($book->find('ul', 0)->find('li') as $elem){
					$elems[] = $elem->plaintext;
				}

				array_push($WeeklyBookDatas ,new WeeklyBookData(
					$date, $rank++, $title, $author, $elems[0], $elems[1], $elems[2], $elems[3]));
			}
		}else {
			throw new Exception("**********Failure!!  Can't get file!!***********");
		}
	}
	return $WeeklyBookDatas;
}


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
	private $price;//価格(税込み)
	private $publishNum;//推定売上部数

	public function __construct($date, $rank, $title, $author, $company, $publishDate, $price, $publishNum){
		$this->date = $date;
		$this->rank = $rank;
		$this->title = $title;
		$this->author = $author;
		$this->company = str_replace(encodeToSJIS("出版社："), "", $company);
		$this->publishDate = str_replace(encodeToSJIS("発売日："), "", $publishDate);
		$this->price = str_replace(array(encodeToSJIS("価格："), encodeToSJIS("円(税込)")), "", $price);
		$this->publishNum = str_replace(array(encodeToSJIS("推定売上部数："), encodeToSJIS(","), encodeToSJIS("部")), "", $publishNum);
	}

	//全データをCSV形式の1行にして出力する
	public function getCsvFormattedData(){
		echo $this->date.",".$this->rank.",".$this->title.",".$this->author.",".$this->company.",".$this->publishDate.",".$this->price.",".$this->publishNum."\n";
		return $this->date.",".$this->rank.",".$this->title.",".$this->author.",".$this->company.",".$this->publishDate.",".$this->price.",".$this->publishNum."\n";
	}
}

//週次データ群をCSVファイルとして出力する
function outputCsvData($WeeklyBookDatas){
	try{
		$fileName = "result.csv";
		$file = fopen($fileName, 'w');

		//1行目に項目行を出力
		fwrite($file, mb_convert_encoding("ランキング週,ランキング,タイトル,著者,出版社,発売日,価格(税込み）,推定売上部数\n", "SJIS", "UTF-8"));
		//2行目以下はデータを出力
			foreach ($WeeklyBookDatas as $WeeklyBookData) {
				fwrite($file, $WeeklyBookData->getCsvFormattedData());
			}

			fclose($file);

		}catch(Exception $e){
			throw new Exception("CSVファイル入出力時に例外が発生しました");
		}
	}

//UTF-8  ⇒  SHIFT-JIS　 に文字コード変換
//＊本PHPファイルはUTF-8だが、Excelに出力する値などはShift-JISにする必要があるのでその際使用する
function encodeToSJIS($str){
	return mb_convert_encoding($str, "SJIS", "UTF-8");
}