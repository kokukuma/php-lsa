<?php
// LSA
require_once("./php-lsa.php");

$urls = array("http://homepage2.nifty.com/pdness/jojo/001.html"
		      , "http://homepage2.nifty.com/pdness/jojo/002.html"
		      , "http://homepage2.nifty.com/pdness/jojo/003.html"
		      , "http://homepage2.nifty.com/pdness/jojo/004.html"
		      , "http://homepage2.nifty.com/pdness/jojo/005.html"
);

//-------------------- データの準備
class code{
	var $title;
	var $content;
}

$codes = array();

$c1 = new code();
$c1->title   = "夢と目標へのチャレンジ精神";
$c1->content = "夢を持とう！そして実現するための目標を作ろう！夢と目標は他人から与えられるものではない。そこに対して、継続的に執着心を持って攻め続けよう！絶対に出来る、絶対にあきらめるな！"; 

$c2 = new code();
$c2->title   = "利他の心";
$c2->content = "相手の立場になって考え、行動しよう！自分だけ良ければいいわけはない。和の心と感謝の気持ちを持ち、相手の幸せを本気で考えよう！"; 

$c3 = new code();
$c3->title   = "誠実さと潔さ";
$c3->content = "正直で誠実であり、嘘をつくな！素直に等身大の自分をさらけ出せ。自分のやっていることは道理にかなっているかよく考えよう。すべての責任は自分にあるという潔さを持とう！"; 

$c4 = new code();
$c4->title   = "明朗快活"; 
$c4->content = "明るく、笑顔で、大きな声で挨拶し、常に楽しもう！笑顔は福の神、多いに伝達させ、多いにもらおう！"; 

$c5 = new code();
$c5->title   = "約束を守る"; 
$c5->content = "約束は絶対に守ろう！時間にルーズな奴は仕事もルーズだ。守れなくなるかもしれない時は、事前に素直に謝ろう！約束を守れない奴は信頼もされない。"; 

$c6 = new code();
$c6->title   = "即行動・必ず行動";
$c6->content = "仕事を後回しにするな！今やれることは今すぐやろう。後でやろうは必ずやらなくなる。やることがたくさんある時はいつやるかを決めて、その日までに必ずやろう！それが仕事の基本である。"; 

$c7 = new code();
$c7->title   = "ポジティブに考え、発言しよう";
$c7->content = "他人の意見を否定する際には必ず代案を用意しよう！否定しっぱなしは何も進まない。常に他人を誉め、ポジティブな方向へと導こう！"; 

$c8 = new code();
$c8->title   = "人間力の向上";
$c8->content = "苦労や失敗はチャンスである。それを乗り越えることが自分を高めることになる。間違ってもいいから積極的に考え発言し行動しよう。知らない・分からないは勉強から逃げているだけだ！"; 

$c9 = new code();
$c9->title   = "自己管理能力";
$c9->content = "早寝早起き、整理整頓、スケジュールを立てて、規則正しく行動しよう。それが出来ない人は頭の中もごちゃごちゃだ。決済会社として、セキュリティーと情報管理には万全で望め！"; 

$c10 = new code();
$c10->title   = "人と情報のネットワーク";
$c10->content = "ほうれんそうを徹底せよ！そして積極的に人の話を聞き、意見を言い、相談にのろう！交流を広め、様々な情報を広く集め、社内で共有しよう！情報ほど大事な資産はない。"; 


$codes[0]=$c1;
$codes[1]=$c2;
$codes[2]=$c3;
$codes[3]=$c4;
$codes[4]=$c5;
$codes[5]=$c6;
$codes[6]=$c7;
$codes[7]=$c8;
$codes[8]=$c9;
$codes[9]=$c10;


//-------------------- データの作成
$dataset = array();
// 行動規範の読み込み
foreach ($codes as $key => $value) {
	$dataset[$key] = use_mecab($value->content);

}
// jojo 文の読み込み
$i = 10;
foreach ($urls as $url){
	$r = new HttpRequest($url,HttpRequest::METH_GET);
	try{
		$r->send();
		if ($r->getResponseCode()==200) {
			$str = strip_tags($r->getResponseBody());
			$str = mb_convert_encoding($str, "UTF-8", "SJIS");
			$dataset[$i] = use_mecab($str);
		}
	}catch(HttpException $ex){
		echo $ex;
	}
	$i++;
}



//-------------------- 共起行列の作成
$co = LSA::gen_co_occurrence_matrix($dataset);

//-------------------- 特異値分解の実施 
$svd = LSA::singular_value_decomposition($co,0.6);


//-------------------- 特徴ベクトルの作成
// 
$tname   = "狩野"; 
//$target = "夢を持とう！そして実現するための目標を作ろう！夢と目標は他人から与えられるものではない。そこに対して、継続的に執着心を持って攻め続けよう！絶対に出来る、絶対にあきらめるな！"; 
//$target  = "集中力がある。仕事熱心。あまり遊ばない。ストイック。時々イライラしているときがある。"; 
//$target  = "狩野達也には夢がある。"; 
$target  = "あきらめたらそこで試合終了ですよ。"; 
//$target  = "否定ばかりしている。ネガティブ。やる気なし。休みがち。文句ばかり言って、仕事をしない。給与泥棒"; 
$tkeywords = use_mecab($target);  

$feature_vector = LSA::gen_feature_vector($tkeywords,$svd);
var_dump($feature_vector);


//-------------------- コサインの計算 
echo "\n";
echo "--------- ". $tname ."\n";
echo $target."\n"; 
echo "\n";
foreach ($codes as $key => $value) {
	$cos = LSA::cos($svd->U[$key],$feature_vector);
	echo "title : ".$value->title ."\n";
	echo "estim : ".$cos."\n";
	echo "conte : ".$value->content."\n";
	echo "\n";
}

return;

function use_mecab($content)
{
	$keywords = array();
	$mecab = new Mecab();

	$nodes = $mecab->parseToNode(strip_tags($content));
	
	foreach ($nodes as $node) {
		//if ($node->posid >= 37 && $node->posid <=66) {
		if (($node->posid >= 31 && $node->posid <=68) ||
			($node->posid >= 10 && $node->posid <=12) ) {
			echo "品詞ID" . $node->posid . ":" . $node->surface."<br>\r\n";
			if (!isset($keywords[$node->surface])) {
				$keywords[$node->surface] = 1;
			}else {
				$keywords[$node->surface]++;	
			}
		}
	}
	arsort($keywords);

	return $keywords;
}

?>
