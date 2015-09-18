<?php

class Ms_Model extends Model
{
	
	const TABLE = "ms_type";
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_ms_by_type($type)
	{
		$sql = "SELECT * FROM " . self::TABLE . " WHERE " . Column::MS_TYPE_NAME . " = :type";
		$parameters = array(':type' => $type);
		return $this->_db->unique_result($sql, $parameters);
	}
	
	public function get_ms_list_by_types($types = NULL)
	{
		$sql = "SELECT * FROM " . self::TABLE . " WHERE " . Column::MS_TYPE_NAME . " in ('" . implode("','", $types) . "')";
		return $this->_db->list_result($sql);
	}
	
	// manipulation
	
	public function delete_all()
	{
// 		$sql = "TRUNCATE TABLE `" . Ms_Model::TABLE . "`"; // very quickly than DELETE FROM TABLE
		$sql = "DELETE FROM `" . self::TABLE . "`"; // very quickly than DELETE FROM TABLE
		$this->_db->execute($sql);
	}
	
	public function drop_table()
	{
		$sql = "DROP TABLE `" . self::TABLE . "`";
// 		$sql = "SET FOREIGN_KEY_CHECKS=0;DROP TABLE `" . Ms_Model::TABLE . "`; SET FOREIGN_KEY_CHECKS=1;";
		$this->_db->execute($sql);
	}
	
	public function create_table_if_not_exists()
	{
		$sql = "CREATE TABLE IF NOT EXISTS `" . self::TABLE . "` (
					`MS_TYPE_ID` INT(5) AUTO_INCREMENT NOT NULL,
					`MS_TYPE_NAME` VARCHAR(10) NOT NULL,
					PRIMARY KEY (`MS_TYPE_ID`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$this->_db->execute($sql);
	}
	
	public function insert($type)
	{
		$sql = "INSERT INTO " . self::TABLE . " (" . Column::MS_TYPE_NAME . ") VALUES (:type)";
		$parameters = array(':type' => $type);
		$this->_db->execute($sql, $parameters);
	}
	
}
?>