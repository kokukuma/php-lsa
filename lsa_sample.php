<?php
// LSA
require_once("./php-lsa.php");

//-------------------- データの作成
$name = array("ビール","白米","みかん","ウィスキー","豚肉");
$dataset = array();
$dataset[$name[0]] = array("液体"=>100, "アルコール"=>5, 	"糖質"=>3);
$dataset[$name[1]] = array("液体"=>0, "糖質"=>55,	"タンパク質"=>4,"脂質"=>1);
$dataset[$name[2]] = array("液体"=>0, "アルコール"=>0, 	"糖質"=>10);
$dataset[$name[3]] = array("液体"=>100, "アルコール"=>50);
$dataset[$name[4]] = array("液体"=>0, "タンパク質"=>19,	"脂質"=>15); 



//-------------------- 共起行列の作成
$co = LSA::gen_co_occurrence_matrix($dataset);


//-------------------- 特異値分解の実施 
$svd = LSA::singular_value_decomposition($co,0.8);


//-------------------- 特徴ベクトルの作成
// 
$tname   = array("ステーキ","チューハイ","ウイスキーボンボン");
$target0 = array("液体"=>0,"アルコール"=>0, "タンパク質"=>17,"脂質"=>23); 
$target1 = array("液体"=>100,"アルコール"=>7, "糖質"=>0); 
$target2 = array("液体"=>0,"アルコール"=>2, "糖質"=>7 ,"タンパク質"=>0,"脂質"=>2); 
$target  = array($target0,$target1,$target2);


for ($num=0; $num < 3; $num++) { 

	$feature_vector = LSA::gen_feature_vector($target[$num],$svd);


//-------------------- コサインの計算 
	echo "\n";
	echo "--------- ". $tname[$num] ."\n";
	$cos0 = LSA::cos($svd->U[0],$feature_vector);
	$cos1 = LSA::cos($svd->U[1],$feature_vector);
	$cos2 = LSA::cos($svd->U[2],$feature_vector);
	$cos3 = LSA::cos($svd->U[3],$feature_vector);
	$cos4 = LSA::cos($svd->U[4],$feature_vector);
	echo "cos : ".$name[0] ."		: ".$cos0."\n";
	echo "cos : ".$name[1] ."		: ".$cos1."\n";
	echo "cos : ".$name[2] ."		: ".$cos2."\n";
	echo "cos : ".$name[3] ."	: ".$cos3."\n";
	echo "cos : ".$name[4] ."		: ".$cos4."\n";
	echo "\n";
}

return;

?>
