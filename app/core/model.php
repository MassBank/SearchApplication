<?php
abstract class Model {
	
	protected $_db;
	
	protected function __construct()
	{
		// connect to PDO here.
		$this->_db = new Database();
	}
	
	protected function _get_formatted_sql($sb_sql)
	{
		$sql = $sb_sql->to_string();
		if (Common_Util::endswith($sql, "WHERE")) {
			$sql = str_replace("WHERE", "", $sql);
		}
		
		return trim($sql);
	}
	
	protected function get_compound_model()
	{
		return $this->load_db_model('compound_model');
	}
	
	protected function get_compound_name_model()
	{
		return $this->load_db_model('compound_name_model');
	}
	
	protected function get_instrument_model()
	{
		return $this->load_db_model('instrument_model');
	}
	
	protected function get_ms_model()
	{
		return $this->load_db_model('ms_model');
	}
	
	protected function get_peak_model()
	{
		return $this->load_db_model('peak_model');
	}
	
	protected function get_product_ion_model()
	{
		return $this->load_db_model('product_ion_model');
	}
	
	protected function get_neutral_loss_path_model()
	{
		return $this->load_db_model('neutral_loss_path_model');
	}
	
	protected function get_pre_pro_model()
	{
		return $this->load_db_model('pre_pro_model');
	}
	
	//function to load model on request
	private function load_db_model($name)
	{
		$modelpath = strtolower(APP . '/model/db/'.$name.'.php');
		//try to load and instantiate model
		if (file_exists($modelpath)) {
			require_once $modelpath;
			//break name into sections based on a /
			$parts = explode('/',$name);
			//use last part of array
			$modelName = ucwords(end($parts));
			//instantiate object
			$model = new $modelName();
			//return object to controller
			return $model;
		} else {
			throw new Internal_Exception("Model does not exist: ".$modelpath);
		}
	}
	
}
?>