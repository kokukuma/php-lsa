<?php
class LSA
{

	// 共起行列の作成
	public static function gen_co_occurrence_matrix($dataset)
	{
		// keywordの抽出
		// 各data、各vector要素毎にまだ確認されていないキーワードかどうか確認する.
		$keywords   = array();
		foreach ($dataset as $row_key => $row_value) {
			foreach($row_value as $col_key => $value){
				if (!is_int(array_search($col_key,$keywords))) {
					array_push( $keywords , $col_key);
				}else {
				}
			}
		}

		// 共起行列の作成
		$matrix = array();
		foreach ($dataset as $row_key => $row_value) {
			//１つのdataにつき、1つのvectorを定義しなおす.
			$vector = array();

			//keywordがdataにあればその数を、なければゼロを保存する.
			foreach($keywords as $keyword){
				if (isset($row_value[$keyword])) {
					$vector[$keyword] = $row_value[$keyword];
				}else {
					$vector[$keyword] = 0; 
				}
			}

			// できたvectorをmatrixに格納.
			$matrix[$row_key] = $vector;
		}

		return $matrix;
	} 

	// 特異値分解
	public static function singular_value_decomposition($matrix, $rate)
	{
		// keywordの抽出
		// 各data、各vector要素毎にまだ確認されていないキーワードかどうか確認する.
		$keywords=array();
		foreach ($matrix as $row_key => $row_value) {
			foreach($row_value as $col_key => $value){
				if (!is_int(array_search($col_key,$keywords))) {
					array_push( $keywords , $col_key);
				}else {
				}
			}
		}

		// Eigenに渡す配列を作成
		$to_eigen_matrix = array();	
		foreach ($matrix as $row_key => $vector) {
			$to_eigen_vector = array();	
			foreach ($vector as $key => $value) {
				array_push($to_eigen_vector, $value);
			}
			array_push($to_eigen_matrix, $to_eigen_vector );
		}
		//var_dump($to_eigen_matrix);

		// Eigenを使い特異値分解をする。
		$result = Eigen_Singular_Value_Decomposition($matrix,$rate);

		// 返却するlsa_svdを作成
		$lsm = new lsm();
		$lsm->U = $result->U;
		$lsm->S = $result->S;
		$lsm->V = $result->V;
		$lsm->keywords = $keywords;

		// 返却するlsa_svd作成 LSM
		$i = 0;
		$j = 0;
		foreach ($matrix as $row_key => $vector) {
			foreach($keywords as $keyword){
				$vector[$keyword] = $result->LSM[$i][$j]; 
				$j++;
			}
			$matrix[$row_key] = $vector; 
			$i++;
			$j=0;
		}
		$lsm->LSM = $matrix;

		return $lsm;
	}
	// 特徴ベクトルの生成
	public static function gen_feature_vector($vector,$lsm)
	{
		// keywordの抽出
		// 各data、各vector要素毎にまだ確認されていないキーワードかどうか確認する.
		$keywords=array();
		foreach ($lsm->LSM as $row_key => $row_value) {
			foreach($row_value as $col_key => $value){
				if (!is_int(array_search($col_key,$keywords))) {
					array_push( $keywords , $col_key);
				}else {
				}
			}
		}

		// keywordsに含まれない軸を排除する 
		foreach($keywords as $keyword){
			if (isset($vector[$keyword])) {
				$to_eigen_vector[$keyword] = $vector[$keyword];
			}else {
				$to_eigen_vector[$keyword] = 0; 
			}
		}

		// 特徴ベクトルを計算		
		$res = Eigen_Feature_Vector($lsm->V,$lsm->S,$to_eigen_vector);

		return $res;


	}
	// 特徴ベクトルの生成
	public static function gen_feature_vector2($target,$co,$lsm)
	{
		// vectorを作成.
		$i=0;
		$to_eigen_vector  = array();
		foreach ($co as $key => $vector) {
			//var_dump($vector);
			if(empty($vector[$target]) || $vector[$target]==0){
				$to_eigen_vector[$i]=0;
			}else {
				$to_eigen_vector[$i]=$vector[$target];
			}
			$i++;
		}


		// 特徴ベクトルを計算		
		$res = Eigen_Feature_Vector($lsm->U,$lsm->S,$to_eigen_vector);

		return $res;


	}
	// cosの計算 
	public static function cos($feature_vector1,$feature_vector2)
	{
      //--------------------- Eigen_COS
      $cos = Eigen_COS($feature_vector1, $feature_vector2);
	  return $cos;
	}

}
class lsm 
{
	var $U = array();
	var $S = array();
	var $V = array();
	var $LSM = array();
	var $keywords = array();
}


?>
