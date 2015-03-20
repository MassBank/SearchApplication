<?php

class Controller
{
	
	protected $view;
	
	public function __construct()
	{
		$this->view = new view();
	}

	public function GET($name = NULL)
	{
		// echo $_SERVER['QUERY_STRING'];
		if (empty($_GET[$name])) {
			return NULL;
		}
	
		$vars = $this->exist_params();

		if (sizeof($vars[$name]) > 1) {
			$result = array();
			foreach ($vars[$name] as $value) {
				array_push($result, $value);
			}
			return $result;
		} else {
			return $vars[$name][0];
		}
	}
	
	public function GET_ARRAY($name = NULL)
	{
		if (empty($_GET[$name])) {
			return NULL;
		}
	
		$result = array();
		$vars = $this->exist_params();
		if (sizeof($vars[$name]) > 0) {
			foreach ($vars[$name] as $value) {
				array_push($result, $value);
			}
		}
		return $result;
	}

	public function GET_PARAM($name, $params)
	{
		$name = strtolower($name); // change param name to lower-case
		if ( isset($params[$name]) ) {
			return $params[$name];
		}
		return NULL;
	}
	
	protected function parse_query_str()
	{
		return $this->_parse_str_ext($_SERVER['QUERY_STRING']);
	}
	
	protected function _success($data, $req = false)
	{
		$this->view->render('search/success', $data, $req);
		die;
	}
	//Display an error page if nothing exists
	protected function _error($error)
	{
		require APP . '/core/error.php';
		$this->_controller = new Error($error);
		$this->_controller->index();
		die;
	}
	
	private function exist_params()
	{
		$vars = array();
		foreach (explode('&', $_SERVER['QUERY_STRING']) as $pair) {
			list($key, $value) = explode('=', $pair);
		
			if('' == trim($value)){
				continue;
			}
			$vars[$key][] = $this->get_content(urldecode($value));
		}
		return $vars;
	}
	
	private function _parse_str_ext($to_parse)
	{
		$vars = array();
		foreach (explode('&', $to_parse) as $pair)
		{
			// pull out the names and the values
			list($key, $value) = explode('=', $pair);
	
			// escape empty value
			if ( '' == trim($value) ) {
				continue;
			}
			// change param name to lower-case
			$key = strtolower($key);
	
			// decode the variable name and look for arrays
			$arr = $this->multi_explode(array("[","]"), urldecode($key));
			$key = isset($arr[0]) ? $arr[0] : NULL;
			$index = isset($arr[1]) ? $arr[1] : NULL;
				
			if( !array_key_exists($key, $vars) ) {
				$vars[$key] = array();
			}
	
			// arrays
			if ( isset($index) ) {
				// associative array
				if( $index != "" ) {
					$vars[$key][$index] = $this->get_content($value);
				}
				// ordered array
				else {
					$vars[$key][] = $this->get_content($value);
				}
			}
			// Variables
			else {
				if ( empty($vars[$key]) ) {
					$vars[$key] = $this->get_content($value);
				} else {
					// sample variable came twice should insert into array
					if ( !is_array($vars[$key]) ) {
						$tmp = $vars[$key];
						$vars[$key] = array();
						array_push($vars[$key], $tmp);
					}
					array_push($vars[$key], $this->get_content($value));
				}
			}
		}
		return $vars;
	}
	
	private function multi_explode ($delimiters, $string) {
		$ready = str_replace($delimiters, $delimiters[0], $string);
		$launch = explode($delimiters[0], $ready);
		return  $launch;
	}
	
	private function get_content($content = NULL)
	{
		//  addslashes(urldecode($value));
		$decode = urldecode($content);
		if ( is_numeric($decode) ) {
			return $decode + 0;
		}
		return $decode;

// 		if ( is_numeric( $content ) ) {
// 			return preg_replace("@([^0-9\-])@Ui", "", $content);
// 		} else if ( is_bool( $content ) ) {
// 			return ( $content ? true : false);
// 		} else if ( is_float( $content ) ) {
// 			return preg_replace("@([^0-9\,\.\+\-])@Ui", "", $content);
// 		} else if ( is_string( $content ) ) {
// 			if(filter_var ($content, FILTER_VALIDATE_URL))
// 				return $content;
// 			else if(filter_var ($content, FILTER_VALIDATE_EMAIL))
// 				return $content;
// 			else if(filter_var ($content, FILTER_VALIDATE_IP))
// 				return $content;
// 			else if(filter_var ($content, FILTER_VALIDATE_FLOAT))
// 				return $content;
// 			else
// 				return preg_replace("@([^a-zA-Z0-9\+\-\_\*\@\$\!\;\.\?\#\:\=\%\/\ ]+)@Ui", "", $content);
// 		}
// 		else false;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
//     /**
//      * @var null Database Connection
//      */
//     public $db = null;

//     /**
//      * @var null Model
//      */
//     public $model = null;

//     /**
//      * Whenever controller is created, open a database connection too and load "the model".
//      */
//     function __construct()
//     {
//         $this->openDatabaseConnection();
//         $this->loadModel();
//     }
  
//     /**
//      * Open the database connection with the credentials from application/config/config.php
//      */
//     private function openDatabaseConnection()
//     {
//         // set the (optional) options of the PDO connection. in this case, we set the fetch mode to
//         // "objects", which means all results will be objects, like this: $result->user_name !
//         // For example, fetch mode FETCH_ASSOC would return results like this: $result["user_name] !
//         // @see http://www.php.net/manual/en/pdostatement.fetch.php
//         $options = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING);

//         // generate a database connection, using the PDO connector
//         // @see http://net.tutsplus.com/tutorials/php/why-you-should-be-using-phps-pdo-for-database-access/
//         $this->db = new PDO(DB_TYPE . ':host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET, DB_USER, DB_PASS, $options);
//     }

//     /**
//      * Loads the "model".
//      * @return object model
//      */
//     public function loadModel()
//     {
//         require APP . '/model/model.php';
//         // create new "model" (and pass the database connection)
//         $this->model = new Model($this->db);
//     }
}
?>