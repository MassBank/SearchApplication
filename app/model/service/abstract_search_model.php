<?php
abstract class Abstract_Search_Model extends Model
{
	protected $_instrument_model;
	private $_ms_model;
	
	public function __construct()
	{
		$this->_instrument_model = $this->get_instrument_model();
		$this->_ms_model = $this->get_ms_model();
	}
	
	protected function get_instance_ids_by_types($instrument_types)
	{
		$instrument_ids = array();
		if ( !empty($instrument_types) && sizeof($instrument_types) >= 0 ) {
			if ( !in_array('all', $instrument_types) ) { // is not all instruments
				$instruments = $this->_instrument_model->get_instruments_by_types($instrument_types);
				foreach ($instruments as $instrument) {
					array_push($instrument_ids, $instrument['INSTRUMENT_ID']);
				}
			}
		}
		return $instrument_ids;
	}
	
	protected function get_ms_type_ids_by_types($ms_types)
	{
		$ms_type_ids = array();
		if ( !empty($ms_types) && sizeof($ms_types) >= 0 ) {
			if ( !in_array('all', $ms_types) ) { // is not all instruments
				$ms_list = $this->_ms_model->get_ms_list_by_types($ms_types);
				foreach ($ms_list as $ms_item) {
					array_push($ms_type_ids, $ms_item['MS_ID']);
				}
			}
		}
		return $ms_type_ids;
	}
	
}
?>