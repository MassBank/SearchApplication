<?php
require_once APP . '/model/log/MyLogPHP.class.php';

class Mb_Logger extends MyLogPHP
{
	
	public function __construct()
	{
		parent::__construct(ROOT . 'log/debug.log.csv');
	}
	
}
?>