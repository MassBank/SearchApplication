<?php
class Peak_logic_Model extends Abstract_Logic_Model
{
	private $_peak_model;
	
	public function __construct()
	{
		parent::__construct();
		$this->_peak_model = new Peak_Model();
	}
	 
	public function get_high_intesity_peaks_intersect_by_mz_list($mz_list, $mz_tolerance, $cutoff)
	{
		return $this->get_high_intesity_peaks_by_mz_list($mz_list, $mz_tolerance, $cutoff, "AND");
		// TODO: refactor return peaks not compounds
	}

	public function get_high_intesity_peaks_union_by_mz_list($mz_list, $mz_tolerance, $cutoff)
	{
		return $this->get_high_intesity_peaks_by_mz_list($mz_list, $mz_tolerance, $cutoff, "OR");
		// TODO: refactor return peaks not compounds
	}

	public function get_high_intesity_peaks_diff_intersect_by_mz_list($mz_diff_list, $mz_tolerance, $cutoff)
	{
		return $this->get_high_intesity_peaks_diff_by_mz_list($mz_diff_list, $mz_tolerance, $cutoff, "AND");
		// TODO: refactor return peaks not compounds
	}
	
	public function get_high_intesity_peaks_diff_union_by_mz_list($mz_diff_list, $mz_tolerance, $cutoff)
	{
		return $this->get_high_intesity_peaks_diff_by_mz_list($mz_diff_list, $mz_tolerance, $cutoff, "OR");
		// TODO: refactor return peaks not compounds
	}
	
	private function get_high_intesity_peaks_by_mz_list($mz_list, $mz_tolerance, $cutoff, $op)
	{
		// compound ids match mz
		$compound_ids = array();
		if ( !empty($mz_list) )
		{
			$has_ids_init = false;
			foreach ($mz_list as $mz)
			{
				$mz_arr = Common_Util::get_min_max_by_tolerance($mz, $mz_tolerance);
				if ( !empty($mz_arr) )
				{
					$min_mz = $mz_arr[0];
					$max_mz = $mz_arr[1];
						
					$ids = array();
					$peaks = $this->_peak_model->get_high_intesity_peaks_by_range($min_mz, $max_mz, $cutoff);
					foreach ($peaks as $peak) {
						array_push($ids, $peak[Column::COMPOUND_ID]);
					}
					
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
	}
	
	private function get_high_intesity_peaks_diff_by_mz_list($mz_diff_list, $mz_tolerance, $cutoff, $op)
	{
		$compound_ids = array();
		if ( !empty($mz_diff_list) )
		{
			$has_ids_init = false;
			foreach ($mz_diff_list as $mz_diff)
			{
				$mz_diff_arr = Common_Util::get_min_max_by_tolerance($mz_diff, $mz_tolerance);
				if ( !empty($mz_diff_arr) )
				{
					$min_diff_mz = $mz_diff_arr[0];
					$max_diff_mz = $mz_diff_arr[1];
		
					$ids = array();
					$peaks = $this->_peak_model->get_high_intesity_peaks_diff_by_range($min_diff_mz, $max_diff_mz, $cutoff);
					foreach ($peaks as $peak) {
						array_push($ids, $peak[Column::COMPOUND_ID]);
					}
						
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
	}
	
}