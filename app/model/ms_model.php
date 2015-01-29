<?php

class Ms_Model extends Model
{
	
	const TABLE = "MASS_SPECTROMETRY";
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function delete_all()
	{
// 		$sql = "TRUNCATE TABLE `" . Ms_Model::TABLE . "`"; // very quickly than DELETE FROM TABLE
		$sql = "DELETE FROM `" . Ms_Model::TABLE . "`"; // very quickly than DELETE FROM TABLE
		$this->_db->execute($sql);
	}
	
	public function drop_table()
	{
		$sql = "DROP TABLE `" . Ms_Model::TABLE . "`";
		$this->_db->execute($sql);
	}
	
	public function create_table_if_not_exists()
	{
		$sql = "CREATE TABLE IF NOT EXISTS `" . Ms_Model::TABLE . "` (
					`MS_ID` INT(5) AUTO_INCREMENT NOT NULL,
					`MS_TYPE` VARCHAR(10) NOT NULL,
					PRIMARY KEY (`MS_ID`)) 
				CHARACTER SET utf8 COLLATE utf8_general_ci;";
		$this->_db->execute($sql);
	}
	
	public function insert($type)
	{
		$sql = "INSERT INTO " . Ms_Model::TABLE . " (MS_TYPE) VALUES (:type)";
		$parameters = array(':type' => $type);
		$this->_db->execute($sql, $parameters);
	}
	
	public function get_ms_by_type($type)
	{
		$sql = "SELECT * FROM " . Ms_Model::TABLE . " WHERE MS_TYPE = :type";
		$parameters = array(':type' => $type);
		return $this->_db->uniqueResult($sql, $parameters);
	}
	
	public function get_ms_list_by_types($types = NULL)
	{
		$sql = "SELECT * FROM " . Ms_Model::TABLE . " WHERE MS_TYPE in ('" . implode("','", $types) . "')";
		return $this->_db->listResult($sql);
	}
	
}
?>