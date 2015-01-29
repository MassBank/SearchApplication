<?php

class Instrument_Model extends Model
{
	
	const TABLE = "INSTRUMENT";
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function delete_all()
	{
// 		$sql = "TRUNCATE TABLE `" . Instrument_Model::TABLE . "`"; // very quickly than DELETE FROM TABLE
		$sql = "DELETE FROM `" . Instrument_Model::TABLE . "`"; // very quickly than DELETE FROM TABLE
		$this->_db->execute($sql);
	}
	
	public function drop_table()
	{
		$sql = "DROP TABLE `" . Instrument_Model::TABLE . "`";
		$this->_db->execute($sql);
	}
	
	public function create_table_if_not_exists()
	{
		$sql = "CREATE TABLE IF NOT EXISTS `" . Instrument_Model::TABLE . "` (
					`INSTRUMENT_ID` INT(11) AUTO_INCREMENT NOT NULL,
					`INSTRUMENT_TYPE` VARCHAR(100) NOT NULL,
					PRIMARY KEY (`INSTRUMENT_ID`)
				) 
				CHARACTER SET utf8 COLLATE utf8_general_ci;";
		$this->_db->execute($sql);
	}
	
	public function insert($type)
	{
		$sql = "INSERT INTO " . Instrument_Model::TABLE . " (INSTRUMENT_TYPE) VALUES (:type)";
		$parameters = array(':type' => $type);
		$this->_db->execute($sql, $parameters);
	}
	
	public function get_instruments(){
		return $this->_db->listResult("SELECT * FROM " . Instrument_Model::TABLE . "");
	}

	public function get_instrument_by_type($type)
	{
		$sql = "SELECT * FROM " . Instrument_Model::TABLE . " WHERE INSTRUMENT_TYPE = '" . $type . "'";
		return $this->_db->uniqueResult($sql);
	}
	
	public function get_instruments_by_types($types = NULL)
	{
		$sql = "SELECT * FROM " . Instrument_Model::TABLE . " WHERE INSTRUMENT_TYPE in ('" . implode("','", $types) . "')";
		return $this->_db->listResult($sql);
	}
	
}
?>