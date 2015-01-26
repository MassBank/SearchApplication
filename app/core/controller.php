<?php

class Controller
{
	
	public $view;
	
	public function __construct(){
		$this->view = new view();
	}
	
	//function to load model on request
	public function loadModel($name){
		$modelpath = strtolower('app/model/'.$name.'.php');
		//try to load and instantiate model
		if(file_exists($modelpath)){
				
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
			$this->_error("Model does not exist: ".$modelpath);
			return false;
		}
	}
	
	//Display an error page if nothing exists
	protected function _error($error) {
		require 'app/core/error.php';
		$this->_controller = new Error($error);
		$this->_controller->index();
		die;
	}
	
	public function GET($name = NULL)
	{
		if (empty($_GET[$name])) {
			return false;
		}
		
		$vars = array();
		foreach (explode('&', $_SERVER['QUERY_STRING']) as $pair) {
			list($key, $value) = explode('=', $pair);
		
			if('' == trim($value)){
				continue;
			}
		
			$vars[$key][] = $this->get_content(urldecode($value));
		}
		
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
	
	private function get_content($content = NULL)
	{
		if ( is_numeric( $content ) ) {
			return preg_replace("@([^0-9])@Ui", "", $content);
		} else if ( is_bool( $content ) ) {
			return ( $content ? true : false);
		} else if ( is_float( $content ) ) {
			return preg_replace("@([^0-9\,\.\+\-])@Ui", "", $content);
		} else if ( is_string( $content ) ) {
			if(filter_var ($content, FILTER_VALIDATE_URL))
				return $content;
			else if(filter_var ($content, FILTER_VALIDATE_EMAIL))
				return $content;
			else if(filter_var ($content, FILTER_VALIDATE_IP))
				return $content;
			else if(filter_var ($content, FILTER_VALIDATE_FLOAT))
				return $content;
			else
				return preg_replace("@([^a-zA-Z0-9\+\-\_\*\@\$\!\;\.\?\#\:\=\%\/\ ]+)@Ui", "", $content);
		}
		else false;
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