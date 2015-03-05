<?php

function show_response($result = array()) {
	
	if (array_key_exists('callback', $_GET)) {
		// JSON-P string
		header('Content-Type: text/javascript; charset=utf8');
		header('Access-Control-Allow-Origin: http://www.example.com/');
		header('Access-Control-Max-Age: 3628800');
		header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
	
		$callback = $_GET['callback'];
		echo $callback.'('. json_encode($result) .');';
	
	} else {
		// normal JSON string
		header('Content-Type: application/json; charset=utf8');
	
		echo json_encode($result);
	}
	
}

?>