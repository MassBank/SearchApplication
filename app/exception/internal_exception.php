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
				$message = "Please insert a numeric value of cutoff threshold.";
				break;
			case Code::PARAM_ERROR_INVALID_PEAK:
				$message = "Please insert a valid value of peak.";
				break;
			case Code::PARAM_ERROR_INVALID_VALUE_MODE:
				$message = "Please insert a valid value for parameter 'mode'.";
				break;
			case Code::PARAM_ERROR_INVALID_SEARCH_TYPE:
				$message = "Please insert a valid value for parameter 'search_type'.";
				break;
			case Code::PARAM_ERROR_INVALID_EXACT_MASS:
				$message = "Please insert a numeric value for parameter 'mz'.";
				break;
			case Code::PARAM_ERROR_INVALID_EXACT_MASS_LIST:
				$message = "Please insert numeric values for parameters 'mz[]'.";
				break;
			case Code::PARAM_ERROR_INVALID_EXACT_MASS_DIFF_LIST:
				$message = "Please insert numeric values for parameters 'm_diff[]'.";
				break;
			case Code::PARAM_ERROR_INVALID_RELATIVE_INTENSITY:
				$message = "Please insert a numeric value for parameter 'rel_inte'.";
				break;
			case Code::PARAM_ERROR_INVALID_OPERATOR:
				$message = "Please insert either 'AND' or 'OR' value for parameter 'op'.";
				break;
			default:
				$message = "Unknown error";
				break;
		}
		return $message;
	}
	
}

?>