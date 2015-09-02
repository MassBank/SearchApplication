<?php

class Compound_Name_Model extends Model
{
	
	const TABLE = "compound_name";
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_compound_name_by_name_and_id($compound_id, $compound_name_name)
	{
		$sql = "SELECT * FROM " . self::TABLE . " C WHERE C." . Column::COMPOUND_ID . " =:compound_id AND C." . Column::COMPOUND_NAME_NAME . " =:compound_name_name";
		$params = array(
				":compound_id" => $compound_id,
				":compound_name_name" => $compound_name_name
		);
		return $this->_db->unique_result($sql, $params);
	}
	
	public function delete_all()
	{
// 		$sql = "TRUNCATE TABLE `" . Compound_Name_Model::TABLE . "`"; // very quickly than DELETE FROM TABLE
		$sql = "DELETE FROM `" . self::TABLE . "`"; // very quickly than DELETE FROM TABLE
		$this->_db->execute($sql);
	}
	
	public function drop_table()
	{
		$sql = "DROP TABLE `" . self::TABLE . "`";
// 		$sql = "SET FOREIGN_KEY_CHECKS=0;DROP TABLE `" . Compound_Name_Model::TABLE . "`; SET FOREIGN_KEY_CHECKS=1;";
		$this->_db->execute($sql);
	}
	
	public function create_table_if_not_exists()
	{
		$sql = "CREATE TABLE IF NOT EXISTS `" . self::TABLE . "` (
					`COMPOUND_NAME_ID` INT(11) AUTO_INCREMENT NOT NULL,
					`NAME` VARCHAR(255) NOT NULL,
					`COMPOUND_ID` VARCHAR(10) NOT NULL,
					PRIMARY KEY (`COMPOUND_NAME_ID`),
					FOREIGN KEY (`COMPOUND_ID`) REFERENCES COMPOUND(`COMPOUND_ID`)
				)
				CHARACTER SET utf8 COLLATE utf8_general_ci";
		$this->_db->execute($sql);
	}
	
	public function merge($compound_id, $compound_name_name)
	{
		$compound_name = $this->get_compound_name_by_name_and_id($compound_id, $compound_name_name);
		if ($compound_name != NULL) {
			$this->update($compound_id, $compound_name_name);
		} else {
			$this->insert($compound_id, $compound_name_name);
		}
	}
	
	public function update($compound_id, $compound_name_name)
	{
		$sql = "UPDATE " . self::TABLE . " C SET C." . Column::COMPOUND_NAME_NAME . "=:compound_name_name WHERE C." . Column::COMPOUND_ID . "=:compound_id";
		$params = array(
				":compound_id" => $compound_id,
				":compound_name_name" => $compound_name_name
		);
		return $this->_db->execute($sql, $params);
	}
	
	public function insert($compound_name, $compound_id)
	{
		$sql = "INSERT INTO " . self::TABLE . " (
				NAME, COMPOUND_ID
				) VALUES (:compound_name, :compound_id)";
		$parameters = array(
				':compound_name' => $compound_name, 
				':compound_id' => $compound_id);
		$this->_db->execute($sql, $parameters);
	}
}

?>