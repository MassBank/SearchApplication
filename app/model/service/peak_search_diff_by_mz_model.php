<?php
class Peak_Search_Diff_By_Mz_Model extends Abstract_Search_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index($params)
	{
		#------------------------
		# Peak Difference Search
		#------------------------
		$compound_id = $params->get_compound_id();
		$num_of_mz = $params->get_num_of_mz();
		
		$hit = 0;
		for ( $i = 0; $i < $num_of_mz; $i++ )
		{
			$mz = $params->get_mz_list()[$i];
			$tol = abs($params->get_tolerance());
			$min_mz = $mz - $tol - 0.00001;
			$max_mz = $mz + $tol + 0.00001;
			$val = $params->get_relative_intensity();
			
			$peak_differences = $peak_model->get_peak_differences_by_compound_id($compound_id, $val, $min_mz, $max_mz);
			
			if ( sizeof($peak_differences) ) {
			continue;
			}
			
			$hit++;
			$out_mz = "";
			$cnt = 0;
			$mz1_prev = 0;
			$mz2_prev = 0;
						
			foreach ($peak_differences as $peak_difference)
			{
				$mz1 = $peak_difference['mz1'];
				$mz2 = $peak_difference['mz2'];
				if ( $mz1 >= $mz1_prev + 1 && $mz2 >= $mz2_prev + 1 ) {
				$cnt++;
				}
				$out_mz .= "$mz1,$mz2@";
				$mz1_prev = $mz1;
				$mz2_prev = $mz2;
			}
			if ( $cnt > $max_cnt ) {
			$max_cnt = $cnt ;
			}
			$mz_num = $hit;
		}
	}
}
?>