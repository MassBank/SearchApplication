<?php

class All_Search_Model extends Abstract_Search_Model
{
	private $log;
	private $_compound_model;
	private $_compound_name_model;
	private $_peak_logic_model;
	
	public function __construct()
	{
		parent::__construct();
		$this->log = new Log4Massbank();
	}

	public function index($params)
	{
		$this->log->info(var_export($params, true));
		
		$this->_compound_model = $this->get_compound_model();
		$this->_compound_name_model = $this->get_compound_name_model();
		$this->_peak_logic_model = new Peak_logic_Model();
		
		/* read & init logic specify params */
		$keyword_terms = $params->get_keyword_terms();
		$formula_term = $params->get_formula_term();
		$mol_mass = $params->get_mol_mass();
		$mol_mass_tolerance = $params->get_mol_mass_tolerance();
		
		$mz_list = $params->get_peak_mz();
		$mz_tolerance = $params->get_peak_mz_tolerance();
		$cutoff = $params->get_peak_mz_cutoff();
		$is_peak_mz_diff = $params->get_peak_mz_diff();
		
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
		
		// compound name term
		for ($i = 1; $i < sizeof($keyword_terms); $i++) {
			$keyword_terms[$i] = $this->get_mysql_safe_term($keyword_terms[$i]);
		}
		
		// formula term
		$formula_term = $this->get_mysql_safe_term($formula_term);
		
		// instrument ids by types
		$instrument_ids = $this->get_instance_ids_by_types($instrument_types);
		// mass spectrometry ids by types
		$ms_type_ids = $this->get_ms_type_ids_by_names($ms_types);
		
		// min_mz & max_mz
		$compound_ids = NULL;
		$min_mol_mass = NULL;
		$max_mol_mass = NULL;
		
		$mol_mass_arr = Common_Util::get_min_max_by_tolerance($mol_mass, $mol_mass_tolerance);
		if ( !empty($mol_mass_arr) )
		{
			$min_mol_mass = $mol_mass_arr[0];
			$max_mol_mass = $mol_mass_arr[1];
			
			// compound ids match mz
			if ( $is_peak_mz_diff ) {
				$compound_ids = $this->_peak_logic_model->get_high_intesity_peaks_diff_intersect_by_mz_list($mz_list, $mz_tolerance, $cutoff);
			} else {
				$compound_ids = $this->_peak_logic_model->get_high_intesity_peaks_intersect_by_mz_list($mz_list, $mz_tolerance, $cutoff);
			}
		}
		
		$compounds = $this->_compound_model->get_compounds_by_keywords_and_ids(
				$compound_ids, $keyword_terms, $formula_term, 
				$min_mol_mass, $max_mol_mass,
				$ion_mode, $instrument_ids, $ms_type_ids, $pagination);
		$compounds_count = $this->_compound_model->get_compounds_by_keywords_and_ids(
				$compound_ids, $keyword_terms, $formula_term, 
				$min_mol_mass, $max_mol_mass,
				$ion_mode, $instrument_ids, $ms_type_ids);
		
		$o_compound_ids = array();
		foreach ( $compounds as $compound ) {
			if ( !empty($compound[Column::COMPOUND_ID]) ) {
				array_push($o_compound_ids, $compound[Column::COMPOUND_ID]);
			}
		}
		$o_compound_names = $this->_compound_name_model->get_compound_names_by_ids($o_compound_ids);
		$compound_names = array();
		foreach ($o_compound_names as $compound_name) {
			$compound_names[$compound_name[Column::COMPOUND_ID]][] = $compound_name["NAME"];
		}
		
		$result['data'] = $this->get_output($compounds, $compound_names);
		$result['hit_count'] = $compounds_count;
		return $result;
		
	}
	
	protected function get_output($compounds = NULL, $compound_names = NULL)
	{
		if ( !empty($compounds) )
		{
			foreach ($compounds as $compound)
			{
				if ( !empty($compound[Column::COMPOUND_ID]) )
				{
					$_compound_names = array();
					$data[] = array(
							'compound_id' => $compound[Column::COMPOUND_ID],
							'title' => $compound[Column::COMPOUND_TITLE],
							'names' => $compound_names[$compound[Column::COMPOUND_ID]],
							'ion_mode' => $this->get_value($compound[Column::COMPOUND_ION_MODE]),
							'formula' => $compound[Column::COMPOUND_FORMULA],
							'pubchem' => array(
									"id" => $compound[Column::PUBCHEM_ID],
									"type" => $this->get_db_value($compound, Column::PUBCHEM_ID_TYPE)
							),
							'exact_mass' => $this->get_value($compound[Column::COMPOUND_EXACT_MASS])
					);
				}
			}
		}
		if ( !isset($data) ) {
			$data = array();
		}
		return $data;
	}
	
}
?>