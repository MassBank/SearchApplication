<?php
class MqMessage
{
	/**
	 * @var private
	 */
	private $key = '';
	private $data = array();
	/**
	 * Constructor: Pass over the data we need
	*/
	public function __construct($key, $data) {
		$this->key = $key;
		$this->data = $data;
	}
	/**
	 * getKey: Returns the key
	 */
	public function getKey() {
		return $this->key;
	}
	/**
	 * getData: Returns the data
	 */
	public function getData() {
		return $this->data;
	}
}
?>