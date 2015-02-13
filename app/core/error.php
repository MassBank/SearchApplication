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
		
		$http_status_code = $this->_error->get_http_status_code();
		$data['http_status_code'] = $http_status_code;
		$data['error_message'] = $this->_error->getMessage();
		$data['error_code'] = $this->_error->getCode();
		
		$this->view->render('error/' . $http_status_code, $data);
		
// 		$data['title'] = '404';
// 		$data['error'] = $this->_error;
		
// 		$this->view->rendertemplate('header',$data);
// 		$this->view->render('error/404',$data);
// 		$this->view->rendertemplate('footer',$data);
		
	}
	
}
?>