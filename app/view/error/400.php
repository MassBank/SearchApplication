<?php
	include_once APP . 'view/common/response.php';
	
	$result = array();
	$result['success'] = false;
	$result['error'] = $data;
	show_response( $result );
?>