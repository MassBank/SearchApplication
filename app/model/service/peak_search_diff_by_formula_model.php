<?php
class Peak_Search_Diff_By_Formula_Model extends Abstract_Search_Model
{
	
	private $_neutral_loss_path_model;
	private $_pre_pro_model;
	private $_compound_model;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index($params)
	{
		$this->_neutral_loss_path_model = $this->get_neutral_loss_path_model();
		$this->_pre_pro_model = $this->get_pre_pro_model();
		$this->_compound_model = $this->get_compound_model();
		
		$formula_list = $this->_get_unique_formula_list( $params->get_formula_list() );
		$mode = $params->get_mode();
		$compound_ids = array();
		
		#--------------------
		# SEQUENCE
		#--------------------
		if ( strcasecmp($mode , "SEQ") == 0 )
		{
			$db_result_list = $this->_neutral_loss_path_model->get_compound_ids_by_neutral_loss_path_of_formulas($formula_list);
			foreach ( $db_result_list as $db_result )
			{
				array_push( $compound_ids, $db_result['COMPOUND_ID'] );
			}
		}
		
		#--------------------
		# AND
		#--------------------
		else if ( strcasecmp($mode, 'AND') == 0 )
		{
			asort( $formula_list );
			$concat_formula = implode( ",", $formula_list );
			$compound_nloss_list = $this->_pre_pro_model->get_compound_neutral_loss_of_formulas( $formula_list );
			if ( !empty($compound_nloss_list) )
			{
				$concat_nloss = array();
				foreach ( $compound_nloss_list as $compound_nloss )
				{
					$compound_id = $compound_nloss['COMPOUND_ID'];
					$nloss = $compound_nloss['NEUTRAL_LOSS'];
					
					if ( !isset($concat_nloss[$compound_id]) )
					{
						$concat_nloss[$compound_id] = "";
					}
					$concat_nloss[$compound_id] = $concat_nloss[$compound_id] . $nloss . ",";
				}
				
				foreach ( $concat_nloss as $key => $value )
				{
					$value = rtrim( $value, "," );
					if ( strcmp($value, $concat_formula) == 0 )
					{
						array_push($compound_ids, $key);
					}
				} 
			}
		}
		$compounds = $this->_compound_model->get_compounds_by_ids($compound_ids);
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
	
	private function _get_unique_formula_list($formula_list)
	{
		$result = array();
		if ( sizeof($formula_list) > 0 ) {
			$unique_formula_list = array_unique($formula_list); // remove duplicates
			foreach ( $unique_formula_list as $unique_formula ) {
				array_push($result, $unique_formula);
			}
		}
		return $result;
	}
	
}
?>