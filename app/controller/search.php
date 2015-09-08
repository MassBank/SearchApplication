<?php

require_once APP. '/model/util/common_util.php';
require_once APP. '/entity/constant/db/column.php';
require_once APP. '/entity/constant/error/code.php';
require_once APP. '/entity/constant/constant.php';

class Search extends Controller
{
	/* parameter names */
	// quick search - keyword
	const PARAM_COMPOUND_NAME = 'compound';
	const PARAM_TOLERANCE = 'tol';
	const PARAM_EXACT_MASS = 'mz';
	const PARAM_FORMULA = 'formula';
	const PARAM_OP1 = 'op1';
	const PARAM_OP2 = 'op2';
	// quick search - peak
	const PARAM_PEAK_STRING = 'qpeak';
	const PARAM_CUTOFF = 'cutoff';
	// peak search - peak_by_mz
	const PARAM_FORMULA_LIST = 'formula';
	const PARAM_MZ_LIST = 'mz';
	const PARAM_MZ_DIFF_LIST = 'm_diff';
	const PARAM_REL_INTE = 'rel_inte';
	const PARAM_OP = 'op';
	// peak search - peak_by_formula
	const PARAM_MODE = 'mode';
	// common
	const PARAM_INSTRUMENT = 'inst';
	const PARAM_MS_TYPE = 'ms';
	const PARAM_ION_MODE = 'ion';
	// paging values
	const PARAM_START = 'start';
	const PARAM_LIMIT = 'limit';
	const PARAM_SORT = 'sort';
	const PARAM_ORDER = 'order';
	// default values
	const PARAM_OPERATOR_DEFAULT = 'and';
	const PARAM_START_DEFAULT = 0;
	const PARAM_LIMIT_DEFAULT = 20;
	const PARAM_SORT_DEFAULT = "DESC";
	const PARAM_ION_MODE_DEFAULT = 0;
	const PARAM_MZ_DEFAULT = 0;
	const PARAM_REL_INTE_DEFAULT = 100;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		// index
		echo "start search index <br/>";
		
		$list = array();
		if (!isset($list['A'])){
			$list['A'] = "";
		}
		$list['A'] = $list['A'] . 10 . ",";
		$list['A'] = $list['A'] . 20 . ",";
		$list['A'] = $list['A'] . 30 . ",";
		$list['A'] = $list['A'] . 40 . ",";
		print_r($list);
		
// 		$formula_list = array('coffee', 'brown', 'caffeine');
// 		$where = "";
		
// 		$like = "";
// 		foreach ( $formula_list as $formula ){
// 			if ( $formula != '' ) {
// 				$like .= "%>$formula-";
// 			}
// 		}
// 		$like .= "%";
// 		$where = "PATH LIKE '$like'";
// 		$outer_in = "SELECT DISTINCT ID FROM NEUTRAL_LOSS_PATH WHERE $where";
// 		print $outer_in . "</br>";
		
// 		$num = sizeof($formula_list);
// 		$where = "";
		
// 		$sql = "SELECT * FROM ";
// 		for ($i = 0; $i < $num; $i++) {
// 			if ( $formula_list[$i] != '' ) {
// 				$sql .= "(SELECT ID FROM PRODUCT_ION WHERE FORMULA='$formula_list[$i]') AS t$i";
// 			}
// 			if ( $i > 0 ) {
// 				$where .= "t" . ($i - 1) . ".ID=t" . $i . ".ID";
// 				if ( $i < $num - 1 ) {
// 					$where .= " AND ";
// 				}
// 			}
// 			if ( $i < $num - 1 ) {
// 				$sql .= ", ";
// 			}
// 		}
// 		if ( $where != '' ) {
// 			$sql .= " WHERE $where";
// 		}
// 		print $sql . "</br>";
		
// 		$search_model = new Quick_Search_Keyword_Model();
// 		echo $search_model->check_term("sample 's");
		
// 		echo Common_Util::first_str_replace(" OR A OR B OR C OR ", " OR ", "") . "|" . Common_Util::last_str_replace(" OR A OR B OR C OR ", " OR ", "");
		
// 		$peak_list = array();
// 		foreach (explode("\n", $_GET["qpeak"]) as $line) {
// 			if ( !empty($line) ) {
// 				$line = str_replace("\\r", "", $line);
// 				foreach (explode(";", $line) as $sub_line) {
// 					array_push($peak_list, $sub_line);
// 				}
// 			}
// 		}
// 		print_r($peak_list);
		
// 		$compound_id = "KO002792";
// 		$str_mz = "46.000";
// 		$map_hit_peaks = array();
// 		$p_hit_peak = array("q_mz" => $str_mz);
// 		$map_hit_peaks[$compound_id][] = $p_hit_peak;
// 		$map_hit_peaks[$compound_id][] = $p_hit_peak;
// 		print_r($map_hit_peaks);
// 		foreach ($map_hit_peaks as $key=>$value)
// 		{
// 			print $key . " - " . sizeof($value);
// 		}
		
// 		$map_mz_cnt = array();
// 		$compound_id = "KO002792";
// 		$str_hit_mz = "46.000";
// 		if (empty($map_mz_cnt['0'])) {
// 			$map_mz_cnt['0'] = 0;
// 		}
// 		if (empty($map_mz_cnt[sprintf("%s %s", $compound_id, $str_hit_mz)])) {
// 			$map_mz_cnt[sprintf("%s %s", $compound_id, $str_hit_mz)] = 0;
// 		}
// 		$map_mz_cnt['0']++;
// 		$map_mz_cnt[sprintf("%s %s", $compound_id, $str_hit_mz)]++;
// 		$map_mz_cnt[sprintf("%s %s", $compound_id, $str_hit_mz)]++;
// 		$map_mz_cnt[sprintf("%s %s", $compound_id, $str_hit_mz)]++;
// 		$map_mz_cnt['0']++;
// 		print_r($map_mz_cnt);
		
// 		$compound = "Triacylgly'cerol";
// 		$compound = str_replace("'", "''", $compound);
// 		$where3 = " N.NAME LIKE '\%" . $compound . "\%'";
// 		print $where3;
				
// 		$this->_instrument_model = $this->loadModel('instrument_model');
// 		$instruments = $this->_instrument_model->get_instruments();
// 		print_r($instruments);
// 		echo '</br>';
// 		$instrument_types = array();
// 		array_push($instrument_types, 'LC-ESI-QTOF');
// 		array_push($instrument_types, 'ESI-ITFT');
// 		$instruments = $this->_instrument_model->get_instruments_by_types($instrument_types);
// 		print_r($instruments);
		echo "end search index";
	}
	
	public function quick()
	{
		try {
			$search_type = $this->GET("search_type");
			if ( empty($search_type) ) {
				$search_type = 'keyword';
			}
			
			/* quick search */
			
			$data = NULL;
			$req = NULL;
			
			$log = new Log4Massbank();
			
			// search by keyword
			if ( $search_type == 'keyword' )
			{
				$log->debug( 'START : quick search - by keyword' );
				
				$search_model = new Quick_Search_Keyword_Model();
				$search_params = $this->_get_params_quick_search_by_keyword();
				$result = $search_model->index($search_params);
				$req = $this->_get_request_settings($search_params);
				
				$req["pagination"]["hit_count"] = $result['hit_count'];
				$data = $result['data'];
				
				$log->debug( 'END : quick search - by keyword' );
			}
			
			// search by peak
			else if ( $search_type == 'peak' )
			{
				$log->debug( 'START : quick search - by peak' );
				
				$search_model = new Quick_Search_Peak_Model();
				$search_params = $this->_get_params_quick_search_by_peak();
				$result = $search_model->index($search_params);
				$req = $this->_get_request_settings($search_params);
				
				$req["pagination"]["hit_count"] = $result['hit_count'];
				$data = $result['data'];
				
				$log->debug( 'END : quick search - by peak' );
			}
			
			else 
			{
				throw new Internal_Exception(Code::PARAM_ERROR_INVALID_SEARCH_TYPE);
			}
			
// 			$this->view->rendertemplate('header');
// 			$this->view->render('search/body', $data);
// 			$this->view->rendertemplate('footer');
			$this->_success($data, $req);
		} catch ( Exception $e ) {
			$this->_error($e);
		}
	}
	
	public function peak()
	{
		try {
			$search_type = $this->GET("search_type");
			if ( empty($search_type) ) {
				$search_type = 'peak_by_mz';
			}
			
			/* peak search */
			
			$data = NULL;
			$req = NULL;
			$log = new Log4Massbank();
			
			if ( $search_type == 'peak_by_mz' )
			{
				$log->debug( 'START : peak search - peak by m/z' );
				
				$search_model = new Peak_Search_Peak_By_Mz_Model();
				$search_params = $this->_get_params_peak_search_peak_by_mz();
				$result = $search_model->index($search_params);
				$req = $this->_get_request_settings($search_params);
				
				$req["pagination"]["hit_count"] = $result['hit_count'];
				$data = $result['data'];
				
				$log->debug( 'END : peak search - peak by m/z' );
			}
			else if ( $search_type == 'diff_by_mz' )
			{
				$log->debug( 'START : peak search - peak difference by m/z' );
				
				$search_model = new Peak_Search_Diff_By_Mz_Model();
				$search_params = $this->_get_params_peak_search_diff_by_mz();
				$result = $search_model->index($search_params);
				$req = $this->_get_request_settings($search_params);
				
				$req["pagination"]["hit_count"] = $result['hit_count'];
				$data = $result['data'];
				
				$log->debug( 'END : peak search - peak difference by m/z' );
			}
			/*
			else if ( $search_type == 'peak_by_formula' )
			{
				$search_model = new Peak_Search_Peak_By_Formula_Model();
				$search_params = $this->_get_params_peak_search_peak_by_formula();
				$data = $search_model->index($search_params);
				$req = $this->_get_request_settings($search_params);
			}
			else if ( $search_type == 'diff_by_formula' )
			{
				$search_model = new Peak_Search_Diff_By_Formula_Model();
				$search_params = $this->_get_params_peak_search_diff_by_formula();
				$data = $search_model->index($search_params);
				$req = $this->_get_request_settings($search_params);
			}
			*/
			else
			{
				throw new Internal_Exception(Code::PARAM_ERROR_INVALID_SEARCH_TYPE);
			}
			$this->_success($data, $req);
		} catch ( Exception $e ) {
			$this->_error($e);
		}
	}
	
	private function _get_request_settings($search_params)
	{
		$req = array();
		$full_url = $this->_full_url($_SERVER);
		$req["location"] = parse_url($full_url);
		$req["location"]["url"] = $full_url;
		$req["location"]["origin"] = $this->_url_origin($_SERVER);
		// set parameters & path information
		$params = array();
		foreach ( $this->parse_query_str() as $key => $val ) {
			if ( $key == "url" ) {
			} else {
				array_push($params, array( $key => $val ));
			}
		}
		$req["location"]["parameters"] = $params;
		// set timestamp information
		$objDateTime = new DateTime('NOW');
		$req["timestamp"] = $objDateTime->format(DateTime::ISO8601);
		// set pagination
		$req["pagination"] = array();
		$req["pagination"]["start"] = intval($search_params->get_start());
		$req["pagination"]["limit"] = intval($search_params->get_limit());
		$req["pagination"]["order"] = $search_params->get_order();
		$req["pagination"]["sort"] = $search_params->get_sort();
		return $req;
	}
	
	private function _url_origin($s, $use_forwarded_host=false)
	{
		$ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true:false;
		$sp = strtolower($s['SERVER_PROTOCOL']);
		$protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
		$port = $s['SERVER_PORT'];
		$port = ((!$ssl && $port=='80') || ($ssl && $port=='443')) ? '' : ':'.$port;
		$host = ($use_forwarded_host && isset($s['HTTP_X_FORWARDED_HOST'])) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : null);
		$host = isset($host) ? $host : $s['SERVER_NAME'] . $port;
		return $protocol . '://' . $host;
	}
	
	private function _full_url($s, $use_forwarded_host=false)
	{
		return $this->_url_origin($s, $use_forwarded_host) . $s['REQUEST_URI'];
	}
	
	private function _get_params_quick_search_by_keyword()
	{
		$params = $this->parse_query_str();
				
		$result = new Quick_Search_Keyword_Param();
		$result->set_compound_name_term($this->GET_PARAM(self::PARAM_COMPOUND_NAME, $params));
		$result->set_exact_mass($this->GET_PARAM(self::PARAM_EXACT_MASS, $params)?:self::PARAM_MZ_DEFAULT);
		$result->set_tolerance($this->GET_PARAM(self::PARAM_TOLERANCE, $params));
		$result->set_formula_term($this->GET_PARAM(self::PARAM_FORMULA, $params));
		$result->set_op1(strtoupper($this->GET_PARAM(self::PARAM_OP1, $params)?:self::PARAM_OPERATOR_DEFAULT));
		$result->set_op2(strtoupper($this->GET_PARAM(self::PARAM_OP2, $params)?:self::PARAM_OPERATOR_DEFAULT));
		$this->_get_common_search_params($result, $params);
		$result->set_order($this->GET_PARAM(self::PARAM_ORDER, $params)?:"COMPOUND_ID");
		return $result;
	}
	
	private function _get_params_quick_search_by_peak()
	{
		// TODO: $params = $this->parse_query_str();
		$params = $this->parse_query_str();
		
		$result = new Quick_Search_Peak_Param();
		
		$sb_peak = new String_Builder();
// 		$sb_mz = new String_Builder();
		
		$qpeak = urldecode($this->GET_PARAM(self::PARAM_PEAK_STRING, $params));
		$peak_list = array();
		foreach (explode("\n", $qpeak) as $line) {
			if ( !empty($line) ) {
				$line = str_replace("\\r", "", $line);
				foreach (explode(";", $line) as $sub_line) {
					array_push($peak_list, $sub_line);
				}
			}
		}
		
// 		$is_error = false;
		$max_inte = 0.0;
		
		if ( sizeof( $peak_list ) == 0) {
			throw new Internal_Exception(Code::PARAM_ERROR_NO_PEAK_DATA);
		} else {
			foreach ( $peak_list as $peak )
			{
				$pos_p = strrpos($peak, " ");
				if ( $pos_p >= 0 ) 
				{
					$mz = trim(substr($peak, 0, $pos_p));
					$inte = trim(substr($peak, $pos_p + 1, strlen($peak)));
					if ( (is_numeric($mz) == false) || (is_numeric($inte) == false) ) {
						throw new Internal_Exception(Code::PARAM_ERROR_INVALID_PEAK);
// 						$is_error = true;
// 						break;
					}
					if ( floatval($inte) > $max_inte ) {
						$max_inte = floatval($inte);
					}
				} else {
					throw new Internal_Exception(Code::PARAM_ERROR_INVALID_PEAK);
// 					$is_error = true;
// 					break;
				}
			}
		}
		
		$p_cutoff = $this->GET_PARAM(self::PARAM_CUTOFF, $params);
		if ( /* !$is_error &&  */(is_numeric($p_cutoff) == false) ) {
			throw new Internal_Exception(Code::PARAM_ERROR_INVALID_CUTOFF);
// 			$is_error = true;
		} else {
			foreach ( $peak_list as $peak )
			{
				$pos_p = strrpos($peak, " ");
				$str_mz = trim(substr($peak, 0, $pos_p));
				$str_inte = trim(substr($peak, $pos_p + 1, strlen($peak)));
				$dbl_inte = floatval($str_inte) / $max_inte * 999 + 0.5;
				$rel_inte = intval($dbl_inte);
				
				$sb_peak->append( $str_mz . "," . strval($rel_inte) . "@" );
// 				$sb_mz->append( $mz . "," );
			}
			$result->set_cutoff($p_cutoff);
			$result->set_val($sb_peak->to_string());
		}
		$this->_get_common_search_params($result, $params);
		$result->set_order($this->GET_PARAM(self::PARAM_ORDER, $params)?:"COMPOUND_ID");
		/* GUI supports cutoff param only. */
// 		$result->set_celing($this->GET_PARAM("CEILING", $params));
// 		if ( "LINEAR" == $this->GET_PARAM("WEIGHT", $params) ) {
// 			$result->set_weight(Constant::PARAM_WEIGHT_LINEAR);
// 		} else if ( "SQUARE" == $this->GET_PARAM("WEIGHT", $params) ) {
// 			$result->set_weight(Constant::PARAM_WEIGHT_SQUARE);
// 		}
// 		if ( "LOG" == $this->GET_PARAM("NORM", $params) ) {
// 			$result->set_weight(Constant::PARAM_NORM_LOG);
// 		} else if ( "SQRT" == $this->GET_PARAM("NORM", $params) ) {
// 			$result->set_weight(Constant::PARAM_NORM_SQRT);
// 		}
// 		$result->set_start($this->GET_PARAM("START", $params));
// 		$result->set_tol_unit($this->GET_PARAM("TOLUNIT", $params));
// 		$result->set_col_type($this->GET_PARAM("CORTYPE", $params));
// 		$result->set_floor($this->GET_PARAM("FLOOR", $params));
// 		$result->set_threshold($this->GET_PARAM("NUMTHRESHOLD", $params));
// 		// TODO: CORTHRESHOLD
// 		$result->set_tolerance($this->GET_PARAM("TOLERANCE", $params));
// 		$result->set_num($this->GET_PARAM("NUM", $params));
// 		$result->set_ion_mode($this->GET_PARAM("ION", $params));
		return $result;
	}

	private function _get_params_peak_search_peak_by_mz()
	{
		$params = $this->parse_query_str();
		$result = new Peak_Search_Peak_By_Mz_Param();
		
		$op = $this->GET_PARAM(self::PARAM_OP, $params)?:"AND";
		if ( !empty($op) && ((strcasecmp($op, "AND") == 0) || (strcasecmp($op, "OR") == 0)) ) {
			$result->set_operator($op);
		} else {
			throw new Internal_Exception(Code::PARAM_ERROR_INVALID_OPERATOR);
		}
		
		$_mz_list = $this->GET_PARAM(self::PARAM_MZ_LIST, $params);
		if ( !empty($_mz_list) && $this->is_numeric_array($_mz_list) ) {
			$result->set_mz_list($_mz_list);
		} else {
			throw new Internal_Exception(Code::PARAM_ERROR_INVALID_EXACT_MASS_LIST);
		}
		
		$_rel_inte = $this->GET_PARAM(self::PARAM_REL_INTE, $params);
		if ( !empty($_rel_inte) && is_numeric($_rel_inte) ) {
			$result->set_rel_inte($_rel_inte);
		} else {
			throw new Internal_Exception(Code::PARAM_ERROR_INVALID_RELATIVE_INTENSITY);
		}
// 		$result->set_formula_list($this->GET_PARAM(self::PARAM_FORMULA_LIST, $params));
		$this->_get_common_search_params($result, $params);
		$result->set_order($this->GET_PARAM(self::PARAM_ORDER, $params)?:"COMPOUND_ID");
		return $result;
	}

	private function _get_params_peak_search_diff_by_mz()
	{
		$params = $this->parse_query_str();
		$result = new Peak_Search_Diff_By_Mz_Param();
		
		$op = $this->GET_PARAM(self::PARAM_OP, $params)?:"AND";
		if ( !empty($op) && ((strcasecmp($op, "AND") == 0) || (strcasecmp($op, "OR") == 0)) ) {
			$result->set_operator($op);
		} else {
			throw new Internal_Exception(Code::PARAM_ERROR_INVALID_OPERATOR);
		}
		
		$_mz_diff_list = $this->GET_PARAM(self::PARAM_MZ_DIFF_LIST, $params);
		if ( !empty($_mz_diff_list) && $this->is_numeric_array($_mz_diff_list) ) {
			$result->set_mz_diff_list($_mz_diff_list);
		} else {
			throw new Internal_Exception(Code::PARAM_ERROR_INVALID_EXACT_MASS_DIFF_LIST);
		}
		
		$_rel_inte = $this->GET_PARAM(self::PARAM_REL_INTE, $params);
		if ( !empty($_rel_inte) && is_numeric($_rel_inte) ) {
			$result->set_rel_inte($_rel_inte);
		} else {
			throw new Internal_Exception(Code::PARAM_ERROR_INVALID_RELATIVE_INTENSITY);
		}
// 		$result->set_mz_diff_list($this->GET_PARAM(self::PARAM_MZ_DIFF_LIST, $params));
// 		$result->set_formula_list($this->GET_PARAM(self::PARAM_FORMULA_LIST, $params));
// 		$result->set_rel_inte($this->GET_PARAM(self::PARAM_REL_INTE, $params)?:self::PARAM_REL_INTE_DEFAULT);
		$this->_get_common_search_params($result, $params);
		$result->set_order($this->GET_PARAM(self::PARAM_ORDER, $params)?:"COMPOUND_ID");
		return $result;
	}

	private function _get_params_peak_search_peak_by_formula()
	{
		$params = $this->parse_query_str();
		$result = new Peak_Search_Peak_By_Formula_Param();
		$mode = $this->GET_PARAM(self::PARAM_MODE, $params)?:"AND";
		if ( ! (strcasecmp( $mode, "OR" ) == 0 || strcasecmp( $mode, "AND" ) == 0) ) {
			throw new Internal_Exception(Code::PARAM_ERROR_INVALID_VALUE_MODE);
		}
		$result->set_mode($mode);
		$formula_list = $this->GET_PARAM(self::PARAM_FORMULA_LIST, $params);
		if ( empty($formula_list) ) {
			throw new Internal_Exception(Code::PARAM_ERROR_NO_FORMULA);
		}
		$result->set_formula_list($formula_list);
		$this->_get_common_search_params($result, $params);
		$result->set_order($this->GET_PARAM(self::PARAM_ORDER, $params)?:"COMPOUND_ID");
		return $result;
	}

	private function _get_params_peak_search_diff_by_formula()
	{
		$params = $this->parse_query_str();
		$result = new Peak_Search_Diff_By_Formula_Param();
		$mode = $this->GET_PARAM(self::PARAM_MODE, $params)?:"SEQ";
		if ( ! (strcasecmp( $mode, "SEQ" ) == 0 || strcasecmp( $mode, "AND" ) == 0) ) {
			throw new Internal_Exception(Code::PARAM_ERROR_INVALID_VALUE_MODE);
		}
		$result->set_mode($mode);
		$formula_list = $this->GET_PARAM(self::PARAM_FORMULA_LIST, $params);
		if ( empty($formula_list) ) {
			throw new Internal_Exception(Code::PARAM_ERROR_NO_FORMULA);
		}
		$result->set_formula_list($formula_list);
		$this->_get_common_search_params($result, $params);
		$result->set_order($this->GET_PARAM(self::PARAM_ORDER, $params)?:"COMPOUND_ID");
		return $result;
	}
	
	private function _get_common_search_params($result, $params)
	{
		$result->set_instrument_types($this->GET_PARAM(self::PARAM_INSTRUMENT, $params));
		$result->set_ms_types($this->GET_PARAM(self::PARAM_MS_TYPE, $params));
		$result->set_ion_mode($this->GET_PARAM(self::PARAM_ION_MODE, $params)?:self::PARAM_ION_MODE_DEFAULT);
		// pagination params
		$result->set_start($this->GET_PARAM(self::PARAM_START, $params)?:self::PARAM_START_DEFAULT);
		$result->set_limit($this->GET_PARAM(self::PARAM_LIMIT, $params)?:self::PARAM_LIMIT_DEFAULT);
		$result->set_sort($this->GET_PARAM(self::PARAM_SORT, $params)?:self::PARAM_SORT_DEFAULT);
// 		print_r($result);
		return $result;
	}
	
	private function is_numeric_array(array $array) {
	    foreach ($array as $a => $b) {
	        if (!is_numeric($b)) {
	            return false;
	        }
	    }
	    return true;
	}
	
}
?>