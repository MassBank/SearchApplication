<?php

require_once APP. '/util/common_util.php';
require_once APP. '/entity/column.php';

class Search extends Controller
{
	// parameter names
	const PARAM_ION_MODE = 'ion';
	const PARAM_INSTRUMENT = 'inst';
	const PARAM_MS_TYPE = 'ms';
	const PARAM_COMPOUND_NAME = 'compound';
	const PARAM_TOLERANCE = 'tol';
	const PARAM_EXACT_MASS = 'mz';
	const PARAM_FORMULA = 'formula';
	const PARAM_OP1 = 'op1';
	const PARAM_OP2 = 'op2';
	
	private $_instrument_model;
	private $_ms_model;
	private $_compound_model;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		// index
		$compound = "Triacylgly'cerol";
		$compound = str_replace("'", "''", $compound);
		$where3 = " N.NAME LIKE '\%" . $compound . "\%'";
		print $where3;
				
// 		$this->_instrument_model = $this->loadModel('instrument_model');
// 		$instruments = $this->_instrument_model->get_instruments();
// 		print_r($instruments);
// 		echo '</br>';
// 		$instrument_types = array();
// 		array_push($instrument_types, 'LC-ESI-QTOF');
// 		array_push($instrument_types, 'ESI-ITFT');
// 		$instruments = $this->_instrument_model->get_instruments_by_types($instrument_types);
// 		print_r($instruments);
	}
	
	public function quick()
	{
		
		// quick search
		$search_type = 'keyword';
		$data = NULL;
		
		// search by keyword
		if ( $search_type == 'keyword' )
		{
			$data = $this->quick_search_by_keyword();
		}
		
		// search by peak
		else if ( $search_type == 'peak' )
		{
			
		}
		
		$this->view->rendertemplate('header');
		$this->view->render('search/body', $data);
		$this->view->rendertemplate('footer');
	}
	
	private function quick_search_by_keyword()
	{
		$ion_mode = $this->GET(self::PARAM_ION_MODE);
		$instrument_types = $this->GET_ARRAY(self::PARAM_INSTRUMENT);
		$ms_types = $this->GET_ARRAY(self::PARAM_MS_TYPE);
		$compound_name_term = $this->GET(self::PARAM_COMPOUND_NAME);
		$exact_mass = $this->GET(self::PARAM_EXACT_MASS);
		$tolerance = $this->GET(self::PARAM_TOLERANCE);
		$formula_term = $this->GET(self::PARAM_FORMULA);
		$op1 = $this->GET(self::PARAM_OP1);
		$op2 = $this->GET(self::PARAM_OP2);
		
		$this->_instrument_model = $this->get_instrument_model();
		$this->_compound_model = $this->get_compound_model();
		$this->_ms_model = $this->get_ms_model();
		
		
		$instrument_ids = array();
		$ms_type_ids = array();
		
		// instrument ids
		if ( $instrument_types && sizeof($instrument_types) >= 0 ) {
			if ( !in_array('all', $instrument_types) ) { // is not all instruments
				$instruments = $this->_instrument_model->get_instruments_by_types($instrument_types);
				foreach ($instruments as $instrument) {
					array_push($instrument_ids, $instrument['INSTRUMENT_ID']);
				}
			}
		}
		
		// ms type ids
		if ( $ms_types && sizeof($ms_types) >= 0 ) {
			if ( !in_array('all', $ms_types) ) { // is not all instruments
				$ms_list = $this->_ms_model->get_ms_list_by_types($ms_types);
				foreach ($ms_list as $ms_item) {
					array_push($ms_type_ids, $ms_item['MS_ID']);
				}
			}
		}
		
		// mz1 & mz2
		$mz1 = NULL;
		$mz2 = NULL;
		if ( $exact_mass ) {
			$tolerance = abs($tolerance); // get absolute value of tolerance. (2 or -2 => 2)
			$mz1 = $exact_mass - $tolerance - 0.00001;
			$mz2 = $exact_mass + $tolerance + 0.00001;
		}
		
		// formula term
		$formula_term = str_replace("*", "%", $formula_term);
		
		$compounds = $this->_compound_model->get_keyword_search_compounds(
				$compound_name_term, $mz1, $mz2, $formula_term, $op1, $op2,
				$ion_mode, $instrument_ids, $ms_type_ids);
		
		if ( !empty($compounds) ) {
			foreach ($compounds as $compound) 
			{
				$data[] = array(
						'compound_id' => $compound[Column::COMPOUND_ID],
						'title' => $compound[Column::COMPOUND_TITLE],
						'ion_mode' => $compound[Column::COMPOUND_ION_MODE],
						'formula' => $compound[Column::COMPOUND_FORMULA],
						'exact_mass' => $compound[Column::COMPOUND_EXACT_MASS]
				);
			}
		} else {
			$data = array();
		}
		
		return $this->to_json($data);
	}
	
}
?>