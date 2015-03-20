<?php
	include_once APP . 'view/common/response.php';
	
	$result = array();
	$result['success'] = true;
	$result['data'] = $data;
	$result['request'] = $req;
	show_response( $result );
?>