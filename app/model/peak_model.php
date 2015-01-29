<?php

class Peak_Model extends Model
{
	
	const TABLE = "PEAK";
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function delete_all()
	{
// 		$sql = "TRUNCATE TABLE `" . Ms_Model::TABLE . "`"; // very quickly than DELETE FROM TABLE
		$sql = "DELETE FROM `" . Peak_Model::TABLE . "`"; // very quickly than DELETE FROM TABLE
		$this->_db->execute($sql);
	}
	
	public function drop_table()
	{
		$sql = "DROP TABLE `" . Peak_Model::TABLE . "`";
		$this->_db->execute($sql);
	}
	
	public function create_table_if_not_exists()
	{
		$sql = "CREATE TABLE IF NOT EXISTS `" . Peak_Model::TABLE . "` (
					`PEAK_ID` INT(11) AUTO_INCREMENT NOT NULL,
					`MZ` DOUBLE NOT NULL DEFAULT '0',
					`INTENSITY` DOUBLE NOT NULL DEFAULT '0',
					`RELATIVE_INTENSITY` INT(6) NOT NULL DEFAULT '0',
					`COMPOUND_ID` VARCHAR(10) NOT NULL,
					PRIMARY KEY (`PEAK_ID`),
					FOREIGN KEY (`COMPOUND_ID`) REFERENCES COMPOUND(`COMPOUND_ID`)
				) 
				CHARACTER SET utf8 COLLATE utf8_general_ci;";
		$this->_db->execute($sql);
	}
	
	public function insert($mz, $intensity, $rel_intensity, $compound_id)
	{
		$sql = "INSERT INTO " . Peak_Model::TABLE . " (
				MZ, INTENSITY, RELATIVE_INTENSITY, COMPOUND_ID
				) VALUES (:mz, :intensity, :rel_intensity, :compound_id)";
		$parameters = array(
				':mz' => $mz,
				':intensity' => $intensity,
				':rel_intensity' => $rel_intensity,
				':compound_id' => $compound_id
		);
		$this->_db->execute($sql, $parameters);
	}
	
}
?>