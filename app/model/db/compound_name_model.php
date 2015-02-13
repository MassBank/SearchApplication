<?php

class Compound_Name_Model extends Model
{
	
	const TABLE = "COMPOUND_NAME";
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function delete_all()
	{
// 		$sql = "TRUNCATE TABLE `" . Compound_Name_Model::TABLE . "`"; // very quickly than DELETE FROM TABLE
		$sql = "DELETE FROM `" . Compound_Name_Model::TABLE . "`"; // very quickly than DELETE FROM TABLE
		$this->_db->execute($sql);
	}
	
	public function drop_table()
	{
		$sql = "DROP TABLE `" . Compound_Name_Model::TABLE . "`";
// 		$sql = "SET FOREIGN_KEY_CHECKS=0;DROP TABLE `" . Compound_Name_Model::TABLE . "`; SET FOREIGN_KEY_CHECKS=1;";
		$this->_db->execute($sql);
	}
	
	public function create_table_if_not_exists()
	{
		$sql = "CREATE TABLE IF NOT EXISTS `" . Compound_Name_Model::TABLE . "` (
					`CH_NAME_ID` INT(11) AUTO_INCREMENT NOT NULL,
					`CH_NAME` VARCHAR(255) NOT NULL,
					`COMPOUND_ID` VARCHAR(10) NOT NULL,
					PRIMARY KEY (`CH_NAME_ID`),
					FOREIGN KEY (`COMPOUND_ID`) REFERENCES COMPOUND(`COMPOUND_ID`)
				)
				CHARACTER SET utf8 COLLATE utf8_general_ci";
		$this->_db->execute($sql);
	}
	
	public function insert($compound_name, $compound_id)
	{
		$sql = "INSERT INTO " . Compound_Name_Model::TABLE . " (
				CH_NAME, COMPOUND_ID
				) VALUES (:ch_name, :compound_id)";
		$parameters = array(
				':ch_name' => $compound_name, 
				':compound_id' => $compound_id);
		$this->_db->execute($sql, $parameters);
	}
}

?>