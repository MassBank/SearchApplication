<?php
class Quick_Search_Peak_Model extends Abstract_Search_Model
{
	private $query_mz;
	private $query_val;
	private $map_hit_peak;
	private $map_mz_cnt;
	private $score_list;
	private $m_len;
	private $m_sum;
	private $m_cnt;
	
	private $_compound_model;
	private $_peak_model;
	
	public function __construct()
	{
		parent::__construct();
		$this->query_mz = array();
		$this->query_val = array();
		$this->map_hit_peak = array();
		$this->map_mz_cnt = array();
		$this->score_list = array();
	}
	
	public function index($params)
	{
		$this->_compound_model = $this->get_compound_model();
		$this->_peak_model = $this->get_peak_model();
		
		/* read & init pagination params */
		$pagination = new Pagination_Param();
		$pagination->set_start($params->get_start());
		$pagination->set_limit($params->get_limit());
		$pagination->set_order($params->get_order());
		$pagination->set_sort($params->get_sort());
		
		$this->_set_query_peak($params);
		if ( !$this->_search_peak($params) ) {
			$result['data'] = array();
			$result['hit_count'] = 0;
			return $result;
		}
		$this->_set_score($params);
		
		$compound_ids = array();
		foreach ($this->score_list as $score) {
			array_push($compound_ids, $score["compound_id"]);
		}
		
		$compounds = array();
		$compounds_count = 0;
		if ( !empty($compound_ids) ) {
			$compounds = $this->_compound_model->get_compounds_by_ids($compound_ids, $pagination);
			$compounds_count = sizeof($compound_ids);
		}
		
		$result['data'] = $this->get_output($compounds);
		$result['hit_count'] = $compounds_count;
		return $result;
	}

	protected function get_output($compounds = NULL)
	{
		if ( !empty($compounds) ) {
			foreach ( $compounds as $compound )
			{
				$data[] = array(
						'compound_id' => $compound[Column::COMPOUND_ID],
						'title' => $compound[Column::COMPOUND_TITLE],
						'score' => $this->get_compound_score($compound[Column::COMPOUND_ID]),
						'ion_mode' => $this->get_value($compound[Column::COMPOUND_ION_MODE]),
						'formula' => $compound[Column::COMPOUND_FORMULA],
						'pubchem' => array(
								"id" => $compound[Column::PUBCHEM_ID],
								"type" => $this->get_db_value($compound, Column::PUBCHEM_ID_TYPE)
						),
						'exact_mass' => $this->get_value($compound[Column::COMPOUND_EXACT_MASS])
				);
			}
		} else {
			$data = array();
		}
		return $data;
	}
	
	private function get_compound_score($compound_id)
	{
		$score = 0;
		foreach ( $this->score_list as $score )
		{
			if ( $compound_id == $score["compound_id"] ) {
				$score = $score["score"];
				break;
			}
		}
		return $score;
	}
	
	private function _set_query_peak($params)
	{
		$val_list = explode( "@", $params->get_val() );
		$val_list_count = sizeof($val_list);
		for ($i = 0; $i < $val_list_count; $i++) {
			if ( empty($val_list[$i]) ) continue;
			$peak_parts = explode( ",", $val_list[$i] );
			
			$s_mz = strval( $peak_parts[0] );
			$mz = floatval( $peak_parts[0] );
			$rel_inte = floatval( $peak_parts[1] );
			
			if ( $rel_inte < 1 ) {
				$rel_inte = 1;
			} else if ( $rel_inte > 999 ) {
				$rel_inte = 999;
			}
			if ( $rel_inte < $params->get_cutoff() ) {
				continue;
			}
			
			if ( $params->get_weight() == Constant::PARAM_WEIGHT_LINEAR ) {
				$rel_inte *= $mz / 10;
			}
			else if ( $params->get_weight() == Constant::PARAM_WEIGHT_SQUARE ) {
				$rel_inte *= $mz * $mz / 100;
			}
			if ( $params->get_norm() == Constant::PARAM_NORM_LOG) {
				$rel_inte = log($rel_inte);
			}
			else if ( $params->get_norm() == Constant::PARAM_NORM_SQRT ) {
				$rel_inte = sqrt($rel_inte);
			}
			
			if ( $rel_inte > 0 ) {
				array_push($this->query_mz, $s_mz);
				array_push($this->query_val, $rel_inte);
				$this->m_len += $rel_inte * $rel_inte;
				$this->m_sum += $rel_inte;
				$this->m_cnt++;
			}
		}
		
		if ( $this->m_cnt - 1 < $params->get_threshold() ) {
			$params->set_threshold($this->m_cnt - 1);
		}
	}
	
	private function _search_peak($params)
	{
// 		$isPre = false;
// 		$pre1 = 0;
// 		$pre2 = 0;
		
// 		if ( $params->get_precursor() > 0 ) {
// 			$isPre = true;
// 			$pre1 = queryParam.precursor - 1;
// 			$pre2 = queryParam.precursor + 1;
// // 			sprintf( sqlw1, " and (S.PRECURSOR_MZ is not null and S.PRECURSOR_MZ between %d and %d)", pre1, pre2 );
// 		}
		
		$is_filter = false;
		$target_ids = $this->_get_target_ids($params->get_ion_mode(), $params->get_instrument_types(), $params->get_ms_types());
		if ( empty($target_ids) ) {
			return false;
		}
		$is_filter = true;
		
		$tolerance = $params->get_tolerance();
		$tol_unit = $params->get_tol_unit();
		
		$min_mz = 0;
		$max_mz = 0;
		$mz_count = sizeof($this->query_mz);
		for ( $i = 0; $i < $mz_count; $i++ )
		{
			$str_mz = strval($this->query_mz[$i]);
			$mz = floatval($str_mz);
			$rel_inte = floatval($this->query_val[$i]);
			
			if ( strcasecmp($tol_unit, "unit") == 0 ) {
				$min_mz = $mz - $tolerance;
				$max_mz = $mz + $tolerance;
			} else {
				$min_mz = $mz * (1 - $tolerance / 1000000);
				$max_mz = $mz * (1 + $tolerance / 1000000);
			}
			$min_mz -= 0.00001;
			$max_mz += 0.00001;
			
			$max_intensity_groups = $this->_peak_model->get_max_intensity_groups_by_compound($params->get_cutoff(), $min_mz, $max_mz);
			foreach ( $max_intensity_groups as $max_intensity_group )
			{
				$values = explode(" ", $max_intensity_group["MAX_RELATIVE_INTENSITY"]); // MAX_RELATIVE_INTENSITY
				$compound_id = $values[1];
				
				if ( $is_filter && !in_array($compound_id, $target_ids) ) {
					continue;
				}
				
				$str_hit_val = strval( $values[0] );
				$str_hit_mz = strval( $values[2] );
				$hit_rel_inte = floatval( $str_hit_val );
				$hit_mz = floatval( $str_hit_mz );
				
				if ( $params->get_weight() == Constant::PARAM_WEIGHT_LINEAR ) {
					$hit_rel_inte *= $hit_mz / 10;
				} else if ( $params->get_weight() == Constant::PARAM_WEIGHT_SQUARE ) {
					$hit_rel_inte *= $hit_mz * $hit_mz / 100;
				}
				if ( $params->get_norm() == Constant::PARAM_NORM_LOG ) {
					$hit_rel_inte = log($hit_rel_inte);
				} else if ( $params->get_norm() == Constant::PARAM_NORM_SQRT ) {
					$hit_rel_inte = sqrt($hit_rel_inte);
				}
				
				$p_hit_peak = array(
					"q_mz" => $str_mz,
					"q_val" => $rel_inte,
					"hit_mz" => $str_hit_mz,
					"hit_val" => $hit_rel_inte
				);
				$this->map_hit_peak[$compound_id][] = $p_hit_peak;
				$map_mz_cnt_key = sprintf("%s %s", $compound_id, $str_hit_mz);
				if ( empty($this->map_mz_cnt[$map_mz_cnt_key]) ) {
					$this->map_mz_cnt[$map_mz_cnt_key] = 0;
				}
				$this->map_mz_cnt[$map_mz_cnt_key]++;
			}
		}
		return true;
	}
	
	private function _set_score($params)
	{
		$threshold = $params->get_threshold();
		$cutoff = $params->get_cutoff();
		
		foreach ( $this->map_hit_peak as $compound_id => $hit_peaks )
		{
			$hit_peaks_count = sizeof($hit_peaks);
			if ( $hit_peaks_count <= $threshold ) {
				continue;
			}
			
			$f_sum = 0;
			$f_len = 0;
			$i_cnt = 0;
			
			$peaks = $this->_peak_model->get_peaks_greater_than_cutoff( $compound_id, $cutoff );
			foreach ( $peaks as $peak )
			{
				$str_mz = strval( $peak["MZ"] );
				$str_rel_int = strval( $peak["RELATIVE_INTENSITY"] );
				$mz = floatval( $str_mz );
				$rel_inte = floatval( $str_rel_int );
				
				if ( $params->get_weight() == Constant::PARAM_WEIGHT_LINEAR ) {
					$rel_inte *= $mz / 10;
				} else if ( $params->get_weight() == Constant::PARAM_WEIGHT_SQUARE ) {
					$rel_inte *= $mz * $mz / 100;
				}
				if ( $params->get_norm() == Constant::PARAM_NORM_LOG ) {
					$rel_inte = log($rel_inte);
				} else if ( $params->get_norm() == Constant::PARAM_NORM_SQRT ) {
					$rel_inte = sqrt($rel_inte);
				}
				
				$i_mul = 0;
				$map_mz_cnt_key = sprintf("%s %s", $compound_id, $str_mz);
				if ( isset( $this->map_mz_cnt[$map_mz_cnt_key] ) ) {
					$i_mul = $this->map_mz_cnt[$map_mz_cnt_key];
				}
				if ( $i_mul == 0 ) {
					$i_mul = 1;
				}
				$f_len += $rel_inte * $rel_inte * $i_mul;
				$f_sum += $rel_inte * $i_mul;
				$i_cnt += $i_mul;
			}
			
			$dbl_score = 0;
			if ( $params->get_col_type() == "COSINE" )
			{
				$f_cos = 0;
				foreach ( $hit_peaks as $hit_peak ) {
					$f_cos += floatval($hit_peak["q_val"]) * floatval($hit_peak["hit_val"]);
				}
				
				if ( $this->m_len * $this->m_len == 0 ) {
					$dbl_score = 0;
				} else {
					$dbl_score = $f_cos / sqrt($this->m_len * $f_len);
				}
			}
			if ( $dbl_score >= 0.9999 ) {
				$dbl_score = 0.999999999999;
			} else if ( $dbl_score < 0 ) {
				$dbl_score = 0;
			}
			
			array_push($this->score_list, array(
				"compound_id" => $compound_id,
				"score" => $hit_peaks_count + $dbl_score
			));
			
		}
	}
	
	private function _get_target_ids($ion_mode, $instrument_types, $ms_types)
	{
		$compounds = array();
		$ms_type_ids = $this->get_ms_type_ids_by_names($ms_types);
		$instance_ids = $this->get_instance_ids_by_types($instrument_types);
		$compounds = $this->_compound_model->get_compounds_by_ion_mode($ion_mode, $instrument_types, $ms_type_ids);
		
		$compound_ids = array();
		foreach ($compounds as $compound)
		{
			array_push($compound_ids, $compound[Column::COMPOUND_ID]);
		}
		return $compound_ids;
	}
	
}
?>