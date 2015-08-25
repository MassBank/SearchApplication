<?php
class Quick_Search_Keyword_Model extends Abstract_Search_Model
{
	
	private $_compound_model;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index($params)
	{
		/* read & init logic specify params */
		$compound_name_term = $params->get_compound_name_term();
		$formula_term = $params->get_formula_term();
		$mz = $params->get_exact_mass();
		$tolerance = $params->get_tolerance();
		$op1 = $params->get_op1();
		$op2 = $params->get_op2();
		
		/* read & init common params */
		$instrument_types = $params->get_instrument_types();
		$ms_types = $params->get_ms_types();
		$ion_mode = $params->get_ion_mode();
		
		/* read & init pagination params */
		$pagination = new Pagination_Param();
		$pagination->set_start($params->get_start());
		$pagination->set_limit($params->get_limit());
		$pagination->set_order($params->get_order());
		$pagination->set_sort($params->get_sort());
		
		/* init models */
		$this->_compound_model = $this->get_compound_model();
		
		// compound name term
		$compound_name_term = $this->get_mysql_safe_term($compound_name_term);
		
		// formula term
		$formula_term = $this->get_mysql_safe_term($formula_term);
		
		// min_mz & max_mz
		$min_mz = NULL;
		$max_mz = NULL;
		if ( $mz > 0 ) {
			$tolerance = abs($tolerance); // get absolute value of tolerance. (2 or -2 => 2)
			$min_mz = $mz - $tolerance - 0.00001;
			$max_mz = $mz + $tolerance + 0.00001;
		}
		
		// instrument ids by types
		$instrument_ids = $this->get_instance_ids_by_types($instrument_types);
		// mass spectrometry ids by types
		$ms_type_ids = $this->get_ms_type_ids_by_names($ms_types);
		
		$compounds = $this->_compound_model->get_compounds_by_keywords(
				$compound_name_term, $formula_term, $min_mz, $max_mz, $op1, $op2,
				$ion_mode, $instrument_ids, $ms_type_ids, $pagination);
		
		$compounds_count = intval($this->_compound_model->count_compounds_by_keywords(
				$compound_name_term, $formula_term, $min_mz, $max_mz, $op1, $op2,
				$ion_mode, $instrument_ids, $ms_type_ids));
		
		$result['data'] = $this->get_output($compounds);
		$result['hit_count'] = $compounds_count;
		return $result;
	}
	
	protected function get_output($compounds = NULL)
	{
		if ( !empty($compounds) ) {
			foreach ($compounds as $compound)
			{
				$data[] = array(
					'compound_id' => $compound[Column::COMPOUND_ID],
					'title' => $compound[Column::COMPOUND_TITLE],
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
	
}
?>