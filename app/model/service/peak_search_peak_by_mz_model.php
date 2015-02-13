<?php
class Peak_Search_Peak_By_Mz_Model extends Abstract_Search_Model
{
	
	private $peak_model;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index($params)
	{
		$this->peak_model = $this->get_peak_model();
		#------------------------
		# Peak Search
		#------------------------
		$compound_id = $params->get_compound_id();
		$num_of_mz = $params->get_num_of_mz();
		
		$mz_filters = array();
		for ( $i = 0; $i < $num_of_mz; $i++ )
		{
			$mz = $params->get_mz_list()[$i];
			$tol = abs($params->get_tolerance());
			$min_mz = $mz - $tol - 0.00001;
			$max_mz = $mz + $tol + 0.00001;
			$val = $params->get_relative_intensity();
			array_push($mz_filters, array(
				'min_mz' => $min_mz,
				'max_mz' => $max_mz,
				'rel_int' => $val
			));
		}
		$peaks = $this->peak_model->get_peaks_by_compound_id($compound_id, $mz_filters);
		
		$result_mz_list = array();
		foreach ($peaks as $peak) {
			array_push($result_mz_list, $peak['MZ']);
		}
		return $result_mz_list;
	}
}
?>