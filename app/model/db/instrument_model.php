<?php

class Instrument_Model extends Model
{
	
	const TABLE = "instrument";
	
	public function __construct()
	{
		parent::__construct();
	}

	public function get_instruments(){
		return $this->_db->list_result("SELECT * FROM " . self::TABLE . "");
	}
	
	public function get_instrument_by_type($type)
	{
		$sql = "SELECT * FROM " . self::TABLE . " WHERE INSTRUMENT_TYPE = '" . $type . "'";
		return $this->_db->unique_result($sql);
	}
	
	public function get_instruments_by_types($types = NULL)
	{
		$sql = "SELECT * FROM " . self::TABLE . " WHERE INSTRUMENT_TYPE IN ('" . implode("', '", $types) . "')";
		return $this->_db->list_result($sql);
	}
	
	// manipulation
	
	public function delete_all()
	{
// 		$sql = "TRUNCATE TABLE `" . Instrument_Model::TABLE . "`"; // very quickly than DELETE FROM TABLE
		$sql = "DELETE FROM `" . self::TABLE . "`"; // very quickly than DELETE FROM TABLE
		$this->_db->execute($sql);
	}
	
	public function drop_table()
	{
		$sql = "DROP TABLE `" . self::TABLE . "`";
// 		$sql = "SET FOREIGN_KEY_CHECKS=0;DROP TABLE `" . Instrument_Model::TABLE . "`; SET FOREIGN_KEY_CHECKS=1;";
		$this->_db->execute($sql);
	}
	
	public function create_table_if_not_exists()
	{
		$sql = "CREATE TABLE IF NOT EXISTS `" . self::TABLE . "` (
					`INSTRUMENT_ID` INT(11) AUTO_INCREMENT NOT NULL,
					`INSTRUMENT_TYPE` VARCHAR(100) NOT NULL,
					PRIMARY KEY (`INSTRUMENT_ID`)
				) 
				CHARACTER SET utf8 COLLATE utf8_general_ci;";
		$this->_db->execute($sql);
	}
	
	public function insert($type)
	{
		$sql = "INSERT INTO " . self::TABLE . " (INSTRUMENT_TYPE) VALUES (:type)";
		$parameters = array(':type' => $type);
		$this->_db->execute($sql, $parameters);
	}
	
}
?>