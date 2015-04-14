<?php
class Peak_Search_Peak_By_Mz_Model extends Abstract_Search_Model
{
	
	private $_peak_model;
	private $_compound_model;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index($params)
	{
		/* read & init logic specify params */
		$mz_list = $params->get_mz_list();
		$tolerance = $params->get_tolerance();
		$rel_inte = $params->get_rel_inte();
		$op = $params->get_operator();
		
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
		$this->_peak_model = $this->get_peak_model();
		$this->_compound_model = $this->get_compound_model();
		
		$compound_ids = array();
		$has_ids_init = false;
		
		if ( !empty($mz_list) )
		{
			foreach ($mz_list as $mz)
			{
				if ($mz > 0) {
					$tolerance = abs($tolerance);
					
					$min_mz = $mz - $tolerance - 0.00001;
					$max_mz = $mz + $tolerance + 0.00001;
					
					$ids = $this->get_higher_intensity_peak_compound_ids_by_range($min_mz, $max_mz, $rel_inte);
					if ( strcasecmp($op, 'AND') == 0 ) {
						if ( !empty($ids) && !$has_ids_init ) {
							$compound_ids = $ids;
							$has_ids_init = true;
						} else {
							$compound_ids = array_intersect($compound_ids, $ids);
						}
					} else if ( strcasecmp($op, 'OR') == 0 ) {
						$compound_ids = array_unique(array_merge($compound_ids, $ids));
					}
				}
			}
		}
		
		$compounds = array();
		if ( !empty($compound_ids) )
		{
			// instrument ids by types
			$instrument_ids = $this->get_instance_ids_by_types($instrument_types);
			
			// mass spectrometry ids by types
			$ms_type_ids = $this->get_ms_type_ids_by_names($ms_types);
			
			$compounds = $this->_compound_model->get_compounds_by_ids2($compound_ids, 
					$ion_mode, $instrument_ids, $ms_type_ids, $pagination);
		}
		return $this->get_output($compounds);
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
					'exact_mass' => $this->get_value($compound[Column::COMPOUND_EXACT_MASS])
				);
			}
		} else {
			$data = array();
		}
	
		return $data;
	}
	
	private function get_higher_intensity_peak_compound_ids_by_range($min_mz, $max_mz, $rel_inte)
	{
		$ids = array();
		$peaks = $this->_peak_model->get_high_intesity_peaks_by_range($min_mz, $max_mz, $rel_inte);
		foreach ($peaks as $peak) {
			array_push($ids, $peak[Column::COMPOUND_ID]);
		}
		return $ids;
	}
	
}
?>