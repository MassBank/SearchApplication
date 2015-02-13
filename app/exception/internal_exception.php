<?php

class Internal_Exception extends Exception
{
	
	private $http_status_code;
	
	public function __construct($code) {
		$message = $this->code_to_message($code);
		parent::__construct($message, $code);
	}
	
	public function get_http_status_code() {
		return substr($this->getCode(), 0, 3);
	}
	
	private function code_to_message($code)
	{
		switch ($code) 
		{
			case Code::PARAM_ERROR_NO_PEAK_DATA:
				$message = "No peak input data.";
				break;
			case Code::PARAM_ERROR_ILLEGAL_CUTOFF:
				$message = "Illegal value of cutoff threshold.";
				break;
			case Code::PARAM_ERROR_ILLEGAL_PEAK:
				$message = "Illegal value of peak.";
				break;
			default:
				$message = "Unknown error";
				break;
		}
		return $message;
	}
	
}

?>