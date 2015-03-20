<?php 
class Error extends Controller {
	
	private $_error = null;
	
	public function __construct($error) {
		parent::__construct();
		$this->_error = $error;
	}
	
	public function index() {
// 		$http_status_code = $this->_error["HTTP_STATUS_CODE"];
// 		$error_code = $this->_error["ERROR_CODE"];
// 		$error_msg = $this->_error["ERROR_MESSAGE"];
		
		$error = array();
		$error_message = $this->_error->getMessage();
		$error_code = $this->_error->getCode();
		$sys_error_message = NULL;
		$http_status_code = NULL;
		if ( strpos($error_message, "PDO") !== FALSE || strpos($error_message, "SQL") !== FALSE )
		{
			$error_code = Code::PDO_ERROR;
			$sys_error_message = $error_message;
			$error_message = "Database Error";
		} 
		if ( empty($error_code) ) {
			$error_code = Code::UNKNOWN_ERROR;
		}
		if ( method_exists($this->_error,'get_http_status_code') ) {
			$http_status_code = $this->_error->get_http_status_code();
		} else {
			$http_status_code = 404;
		}
		$error['error_message'] = $error_message;
		if ( !empty($sys_error_message) ) {
			$error['sys_error_message'] = $sys_error_message;
		}
		$error['error_code'] = $error_code;
		$error['http_status_code'] = $http_status_code;
		
		$this->view->render('error/' . $http_status_code, $error);
		
// 		$data['title'] = '404';
// 		$data['error'] = $this->_error;
		
// 		$this->view->rendertemplate('header',$data);
// 		$this->view->render('error/404',$data);
// 		$this->view->rendertemplate('footer',$data);
		
	}
	
}
?>