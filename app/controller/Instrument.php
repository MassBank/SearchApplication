<?php

class Instrument extends Controller
{
	
	private $_instruments;
	
	public function __construct(){
		parent::__construct();
		
		$this->_instruments = $this->loadModel("instrument_model");
	}
	
	public function all(){
		$data['title'] = 'Instruments';
		$data['instruments'] = $this->_instruments->get_instruments();
		echo "all instrument";
// 		print_r($data);
	}
	
}
?>