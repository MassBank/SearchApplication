<?php
	include_once APP . 'view/common/response.php';

	$result = array();
	$result['success'] = true;
	$result['data'] = $data;
	show_response( $result );
?>