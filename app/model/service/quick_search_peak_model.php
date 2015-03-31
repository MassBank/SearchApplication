<?php
class Quick_Search_Peak_Model extends Abstract_Search_Model
{
	private $query_mz;
	private $query_val;
	private $map_hit_peak;
	private $map_mz_cnt;
	private $score_list;
	private $m_f_len;
	private $m_f_sum;
	private $m_i_cnt;
	
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
		
		$this->_set_query_peak($params);
		if ( !$this->_search_peak($params) ) {
			return 0;
		}
		$this->_set_score($params);
		return $this->get_output(null);
	}
	
	private function _set_query_peak($params)
	{
		$val_list = explode( "@", $params->get_val() );
		$val_list_count = sizeof($val_list);
		for ($i = 0; $i < $val_list_count; $i++) {
			if ( empty($val_list[$i]) ) continue;
			$peak_parts = explode( ",", $val_list[$i] );
			
			$s_mz = strval( $peak_parts[0] );
			$f_mz = floatval( $peak_parts[0] );
			$f_val = floatval( $peak_parts[1] );
			
			if ( $f_val < 1 ) {
				$f_val = 1;
			} else if ( $f_val > 999 ) {
				$f_val = 999;
			}
			if ( $f_val < $params->get_cutoff() ) {
				continue;
			}
			
			if ( $params->get_weight() == Constant::PARAM_WEIGHT_LINEAR ) {
				$f_val *= $f_mz / 10;
			}
			else if ( $params->get_weight() == Constant::PARAM_WEIGHT_SQUARE ) {
				$f_val *= $f_mz * $f_mz / 100;
			}
			if ( $params->get_norm() == Constant::PARAM_NORM_LOG) {
				$f_val = log($f_val);
			}
			else if ( $params->get_norm() == Constant::PARAM_NORM_SQRT ) {
				$f_val = sqrt($f_val);
			}
			
			if ( $f_val > 0 ) {
				array_push($this->query_mz, $s_mz);
				array_push($this->query_val, $f_val);
				$this->m_f_len += $f_val * $f_val;
				$this->m_f_sum += $f_val;
				$this->m_i_cnt++;
			}
		}
		
		if ( $this->m_i_cnt - 1 < $params->get_threshold() ) {
			$params->set_threshold($this->m_i_cnt - 1);
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
		
		$f_min_mz = 0;
		$f_max_mz = 0;
		$query_mz_length = sizeof($this->query_mz);
		for ($i = 0; $i < $query_mz_length; $i++)
		{
			$str_mz = strval($this->query_mz[$i]);
			$f_mz = floatval($str_mz);
			$f_val = floatval($this->query_val[$i]);
			$f_tolerance = $params->get_tolerance();
			
			if ( $params->get_tol_unit() == "unit" ) {
				$f_min_mz = $f_mz - $f_tolerance;
				$f_max_mz = $f_mz + $f_tolerance;
			} else {
				$f_min_mz = $f_mz * (1 - $f_tolerance / 1000000);
				$f_max_mz = $f_mz * (1 + $f_tolerance / 1000000);
			}
			$f_min_mz -= 0.00001;
			$f_max_mz += 0.00001;
			
			$max_intensity_groups = $this->_peak_model->get_max_intensity_groups_by_compound($params->get_cutoff(), $f_min_mz, $f_max_mz);
			$max_intensity_groups_length = sizeof($max_intensity_groups);
			foreach ( $max_intensity_groups as $max_intensity_group)
			{
				$values = explode(" ", $max_intensity_group["MAX_RELATIVE_INTENSITY"]); // MAX_RELATIVE_INTENSITY
				$compound_id = $values[1];
				
				if ($is_filter && !in_array($compound_id, $target_ids)) {
					continue;
				}
				
				$str_hit_val = strval( $values[0] );
				$str_hit_mz = strval( $values[2] );
				$f_hit_val = floatval( $str_hit_val );
				$f_hit_mz = floatval( $str_hit_mz );
				
				if ( $params->get_weight() == Constant::PARAM_WEIGHT_LINEAR ) {
					$f_hit_val *= $f_hit_mz / 10;
				} else if ( $params->get_weight() == Constant::PARAM_WEIGHT_SQUARE ) {
					$f_hit_val *= $f_hit_mz * $f_hit_mz / 100;
				}
				if ( $params->get_norm() == Constant::PARAM_NORM_LOG ) {
					$f_hit_val = log($f_hit_val);
				} else if ( $params->get_norm() == Constant::PARAM_NORM_SQRT ) {
					$f_hit_val = sqrt($f_hit_val);
				}
				
				$p_hit_peak = array(
					"q_mz" => $str_mz,
					"q_val" => $f_val,
					"hit_mz" => $str_hit_mz,
					"hit_val" => $f_hit_val
				);
				$this->map_hit_peak[$compound_id][] = $p_hit_peak;
				$map_mz_cnt_key = sprintf("%s %s", $compound_id, $str_hit_mz);
				if (empty($this->map_mz_cnt[$map_mz_cnt_key])) {
					$this->map_mz_cnt[$map_mz_cnt_key] = 0;
				}
				$this->map_mz_cnt[$map_mz_cnt_key]++;
			}
		}
		return true;
	}
	
	private function _set_score($params)
	{
		foreach ($this->map_hit_peak as $compound_id => $hit_peaks)
		{
			$i_hit_num = sizeof($hit_peaks);
			if ( $i_hit_num <= $params->get_threshold() ) {
				continue;
			}
			
			$f_sum = 0;
			$f_len = 0;
			$i_cnt = 0;
			
			$peaks = $this->_peak_model->get_peaks_greater_than_cutoff($compound_id, $params->get_cutoff());
			foreach ($peaks as $peak)
			{
				$str_mz = $peak["MZ"];
				$str_rel_int = $peak["RELATIVE_INTENSITY"];
				$f_mz = floatval( $str_mz );
				$f_val = floatval( $str_rel_int );
				
				if ( $params->get_weight() == Constant::PARAM_WEIGHT_LINEAR ) {
					$f_val *= $f_mz / 10;
				} else if ( $params->get_weight() == Constant::PARAM_WEIGHT_SQUARE ) {
					$f_val *= $f_mz * $f_mz / 100;
				}
				if ( $params->get_norm() == Constant::PARAM_NORM_LOG ) {
					$f_val = log($f_val);
				} else if ( $params->get_norm() == Constant::PARAM_NORM_SQRT ) {
					$f_val = sqrt($f_val);
				}
				
				$i_mul = 0;
				$map_mz_cnt_key = sprintf("%s %s", $compound_id, $str_mz);
				if ( isset( $this->map_mz_cnt[$map_mz_cnt_key] ) ) {
					$i_mul = $this->map_mz_cnt[$map_mz_cnt_key];
				}
				if ( $i_mul == 0 ) {
					$i_mul = 1;
				}
				$f_len += $f_val * $f_val * $i_mul;
				$f_sum += $f_val * $i_mul;
				$i_cnt += $i_mul;
			}
			
			$dbl_score = 0;
			if ( $params->get_col_type() == "COSINE" )
			{
				$f_cos = 0;
				foreach ( $hit_peaks as $hit_peak ) {
					$f_cos += floatval($hit_peak["q_val"]) * floatval($hit_peak["hit_val"]);
				}
				
				if ( $this->m_f_len * $this->m_f_len == 0 ) {
					$dbl_score = 0;
				} else {
					$dbl_score = $f_cos / sqrt($this->m_f_len * $f_len);
				}
			}
			if ( $dbl_score >= 0.9999 ) {
				$dbl_score = 0.999999999999;
			} else if ( $dbl_score < 0 ) {
				$dbl_score = 0;
			}
			
			
			array_push($this->score_list, array(
				"compound_id" => $compound_id,
				"score" => $i_hit_num + $dbl_score
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
	
	protected function get_output($compounds = NULL)
	{
		$result = array();
		foreach ($this->score_list as $score)
		{
			$compound_id = $score["compound_id"];
			$compound = $this->_compound_model->get_compound_by_id($compound_id);
			if ( !empty($compound) ) 
			{
				array_push($result, array(
					"compound_id" => $compound[Column::COMPOUND_ID],
					"title" => $compound[Column::COMPOUND_TITLE],
					"score" => $this->get_value($score["score"]),
					"ion_mode" => $this->get_value($compound[Column::COMPOUND_ION_MODE]),
					"formula" => $compound[Column::COMPOUND_FORMULA],
					'exact_mass' => $this->get_value($compound[Column::COMPOUND_EXACT_MASS])
				));
			}
		}
		return $result;
	}
	
}
?>