<?php
/**
* ORICON STYLEの文庫本週次ランキングデータをダウンロード
* 注：スタンドアロンで使用するスクリプト
*/
// header("Content-Type: text/html; charset=SHIFT-JIS");

ini_set("max_execution_time",3600);//ファイル実行可能時間を長くしておく

//PHP Simple HTML DOM Parser Manualの読み込み
require_once 'lib/simple_html_dom.php';

define("URL", "http://www.boxofficemojo.com/intl/japan/?yr=%d&wk=%d&currency=local&p=.htm");
define("FILE_NAME", encodeToSJIS("週末興行収入 週次ランキング.csv"));

//取得する年ごとの週数の組み合わせ
$weeksByYear = array(2014=>46,2013=>52,2012=>52,2011=>52,2010=>53,2009=>52,2008=>52,2007=>51,2006=>52,2005=>51,2004=>53,2003=>51,2002=>50);


//最初にCSVファイルに入った以前のデータを空にしておく
$file = fopen(FILE_NAME, 'w');
ftruncate($file, 0);
fclose($file);

//再び追記モードでファイルを開く
$file = fopen(FILE_NAME, 'a');
fwrite($file, encodeToSJIS("ランキング年,ランキング週,ランキング,タイトル,週末興行収入,スクリーン数,累計興行収入,公開後経過週\n"));//1行目に項目行を出力

foreach ($weeksByYear as $year => $weekNum) { 
	for($week=$weekNum; $week>=1; $week--){
		//DOMをパースし必要なデータを変数に代入
		$WeeklyMovieDatas = getWeeklyMovieDatas($year, $week);
		//CSVデータ出力＆結果メッセージ出力
		outputCsvData($file, $WeeklyMovieDatas);
	}
}

//終了処理
fclose($file);
echo "******** Success!! **********";

///////////////////////////
///// 以下クラス・メソッド群 /////
///////////////////////////

//引数指定された日にち付けのURLの売上TOP30の週次データを取得する
function getWeeklyMovieDatas($year, $week){
	$WeeklyMovieDatas = array();
	
	$html = file_get_html(URL, $year, $week);

	if($html != ""){
		foreach($html->find('div[class=inner] div[class=wrap-text]') as $book){

			//HTMLが特徴的（idなどを割り振っているなど）でないため、結局ここの取得の部分ができなさそう。
			// $title = $book->find('h2', 0)->plaintext;
			// $author = $book->find('p', 0)->plaintext;

			// $elems = array();
			// foreach($book->find('ul', 0)->find('li') as $elem){
			// 	$elems[] = $elem->plaintext;
			// }

			// array_push($WeeklyMovieDatas ,new WeeklyMovieData(
			// 	$year, $week, $rank, $title, $weekendGross, $screenNum, $grossSum, $weekAfterBegin));
		}
	}else {
		print("指定の週[".$year."-".$week."]のファイルが取得できませんでした。")
	}
	return $WeeklyMovieDatas;
}

//週次データ群をCSVファイルとして出力する
function outputCsvData($file, $WeeklyMovieDatas){
	//1WeeklyMovieDataデータあたり1行で出力
	foreach ($WeeklyMovieDatas as $WeeklyMovieData) {
		fwrite($file, $WeeklyMovieData->getCsvFormattedData());
	}
}

//UTF-8  ⇒  SHIFT-JIS　 に文字コード変換
//＊本PHPファイルはUTF-8だが、Excelに出力する値などはShift-JISにする必要があるのでその際使用する
function encodeToSJIS($str){
	return mb_convert_encoding($str, "SJIS", "UTF-8");
}


/**
* 1つの週次本データを表すオブジェクト
*/
class WeeklyMovieData{
	private $year;//ランキング年
	private $week;//ランキング週
	private $rank;//ランキング
	private $title;//タイトル
	private $weekendGross;//週末興行収入
	private $screenNum;//スクリーン数
	private $grossSum;//累計興行収入
	private $weekAfterBegin;//公開後経過週

	public function __construct($year, $week, $rank, $title, $weekendGross, $screenNum, $grossSum, $weekAfterBegin){
		$this->year = $year;
		$this->week = $week;
		$this->rank = $rank;
		$this->title = str_replace(encodeToSJIS(","), "", $title);
		$this->weekendGross = str_replace(array(encodeToSJIS(","), encodeToSJIS("\\")), "", $weekendGross);
		$this->screenNum = $screenNum;
		$this->grossSum = str_replace(array(encodeToSJIS(","), encodeToSJIS("\\")), "", $grossSum);
		$this->weekAfterBegin = $weekAfterBegin;
	}

	//全データをCSV形式の1行にして出力する
	public function getCsvFormattedData(){
		return $this->year.",".$this->week.",".$this->rank.",".$this->title.",".$this->weekendGross.",".$this->screenNum.",".$this->grossSum.",".$this->weekAfterBegin."\n";
	}
}<?php
/**
* ORICON STYLEの文庫本週次ランキングデータをダウンロード
* 注：スタンドアロンで使用するスクリプト
*/
// header("Content-Type: text/html; charset=SHIFT-JIS");

ini_set("max_execution_time",3600);//ファイル実行可能時間を長くしておく

//PHP Simple HTML DOM Parser Manualの読み込み
require_once 'lib/simple_html_dom.php';

define("URL_PRE", "http://www.boxofficemojo.com/intl/japan/?yr=%d&wk=%d&currency=local&p=.htm");
define("FILE_NAME", encodeToSJIS("週末興行収入 週次ランキング.csv"));

//取得する年ごとの週数の組み合わせ
$weeksByYear = array(2014=>46,2013=>52,2012=>52,2011=>52,2010=>53,2009=>52,2008=>52,2007=>51,2006=>52,2005=>51,2004=>53,2003=>51,2002=>50);


//最初にCSVファイルに入った以前のデータを空にしておく
$file = fopen(FILE_NAME, 'w');
ftruncate($file, 0);
fclose($file);

//再び追記モードでファイルを開く
$file = fopen(FILE_NAME, 'a');
fwrite($file, encodeToSJIS("ランキング年,ランキング週,ランキング,タイトル,週末興行収入,スクリーン数,累計興行収入,公開後経過週\n"));//1行目に項目行を出力

foreach ($weeksByYear as $year => $weekNum) { 
	for($week=$weekNum; $week>=1; $week--){
	//DOMをパースし必要なデータを変数に代入
	$WeeklyMovieDatas = getWeeklyMovieDatas($date);
	//CSVデータ出力＆結果メッセージ出力
	outputCsvData($file, $WeeklyMovieDatas);
}
}

//終了処理
fclose($file);
echo "******** Success!! **********";

///////////////////////////
///// 以下クラス・メソッド群 /////
///////////////////////////

//引数指定された日にち付けのURLの売上TOP30の週次データを取得する
function getWeeklyMovieDatas($date){
	$WeeklyMovieDatas = array();
	
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

				array_push($WeeklyMovieDatas ,new WeeklyMovieData(
					$date, $rank++, $title, $author, $elems[0], $elems[1], $elems[2], $elems[3]));
			}
		}else {
			throw new Exception("**********Failure!!  Can't get file!!***********");
		}
	}
	return $WeeklyMovieDatas;
}

//週次データ群をCSVファイルとして出力する
function outputCsvData($file, $WeeklyMovieDatas){
	//1WeeklyMovieDataデータあたり1行で出力
	foreach ($WeeklyMovieDatas as $WeeklyMovieData) {
		fwrite($file, $WeeklyMovieData->getCsvFormattedData());
	}
}

//UTF-8  ⇒  SHIFT-JIS　 に文字コード変換
//＊本PHPファイルはUTF-8だが、Excelに出力する値などはShift-JISにする必要があるのでその際使用する
function encodeToSJIS($str){
	return mb_convert_encoding($str, "SJIS", "UTF-8");
}


/**
* 1つの週次本データを表すオブジェクト
*/
class WeeklyMovieData{
	private $year;//ランキング年
	private $week;//ランキング週
	private $rank;//ランキング
	private $title;//タイトル
	private $weekendGross;//週末興行収入
	private $screenNum;//スクリーン数
	private $grossSum;//累計興行収入
	private $weekAfterBegin;//公開後経過週

	public function __construct($year, $week, $rank, $title, $weekendGross, $screenNum, $grossSum, $weekAfterBegin){
		$this->year = $year;
		$this->week = $week;
		$this->rank = $rank;
		$this->title = str_replace(encodeToSJIS(","), "", $title);
		$this->weekendGross = str_replace(array(encodeToSJIS(","), encodeToSJIS("\\")), "", $weekendGross);
		$this->screenNum = $screenNum;
		$this->grossSum = str_replace(array(encodeToSJIS(","), encodeToSJIS("\\")), "", $grossSum);
		$this->weekAfterBegin = $weekAfterBegin;
	}

	//全データをCSV形式の1行にして出力する
	public function getCsvFormattedData(){
		return $this->year.",".$this->week.",".$this->rank.",".$this->title.",".$this->weekendGross.",".$this->screenNum.",".$this->grossSum.",".$this->weekAfterBegin."\n";
	}
}