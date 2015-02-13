<?php
class Peak_Search_Diff_By_Formula_Model extends Abstract_Search_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index($params)
	{
		$formula_list = $params->get_formula_list();
		$mode = $params->get_mode();
		#--------------------
		# SEQUENCE
		#--------------------
		if ( strcasecmp($mode , "SEQ") == 0 )
		{
			
		}
		#--------------------
		# AND
		#--------------------
		else if ( strcasecmp($mode, 'AND') == 0 )
		{
			
		}
	}
}
?>