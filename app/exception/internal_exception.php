<?php

class Internal_Exception extends Exception
{
	
	private $http_status_code;
	
	public function __construct($code, $message = NULL) {
		if ( empty($message) ) {
			$message = $this->code_to_message($code);
		}
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
				$message = "No 'peak' input data.";
				break;
			case Code::PARAM_ERROR_NO_FORMULA:
				$message = "No 'formula' input data.";
				break;
				
			case Code::PARAM_ERROR_INVALID_CUTOFF:
				$message = "Invalid value of cutoff threshold.";
				break;
			case Code::PARAM_ERROR_INVALID_PEAK:
				$message = "Invalid value of peak.";
				break;
			case Code::PARAM_ERROR_INVALID_VALUE_MODE:
				$message = "Invalid value for parameter 'mode'.";
				break;
			case Code::PARAM_ERROR_INVALID_SEARCH_TYPE:
				$message = "Invalid value for parameter 'search_type'.";
				break;
				
			default:
				$message = "Unknown error";
				break;
		}
		return $message;
	}
	
}

?>