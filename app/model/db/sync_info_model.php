<?php

require_once APP . '/model/log/log4massbank.php';

class Sync_Info_Model extends Model
{
	const TABLE = "sync_info";
	
	private $log;
	
	public function __construct()
	{
		parent::__construct();
		$this->log = new Log4Massbank();
	}

	public function get_sync_info_list($pagination)
	{
		$sb_sql = new String_Builder();
		$sb_sql->append("SELECT * FROM " . self::TABLE . " AS C");
		$this->append_pagination_clause($sb_sql, $pagination);
		$sql = $this->_get_formatted_sql($sb_sql);
		$this->log->debug($sql);
		return $this->_db->list_result($sql);
	}
	
	public function delete_sync_info($sync_id)
	{
		$sql = "DELETE FROM `" . self::TABLE . "` WHERE " . Column::SYNC_ID . " = " . $sync_id;
		$this->_db->execute($sql);
	}
	
	public function insert($repo, $resource, $media, $updated, $timestamp)
	{
		$sql = "INSERT INTO " . self::TABLE . " (" . Column::SYNC_REPOSITORY . "," . Column::SYNC_RESOURCE . "," .
				Column::SYNC_MEDIA_TYPE . "," . Column::SYNC_UPDATED . "," . Column::SYNC_TIMESTAMP . ") VALUES (:repo,:resource,:media,:updated,:timestamp)";
		$parameters = array(
				':repo' => $repo,
				':resource' => $resource,
				':media' => $media,
				':updated' => $updated,
				':timestamp' => $timestamp);
		$this->_db->execute($sql, $parameters);
	}
	
	public function drop_table()
	{
		$sql = "DROP TABLE `" . self::TABLE . "`";
		$this->_db->execute($sql);
	}
	
	public function create_table_if_not_exists()
	{
		$sql = "CREATE TABLE IF NOT EXISTS `" . self::TABLE . "` (
					`SYNC_ID` INT(11) AUTO_INCREMENT NOT NULL,
					`REPOSITORY` VARCHAR(100) NOT NULL,
					`RESOURCE` VARCHAR(255) NOT NULL,
					`MEDIA_TYPE` VARCHAR(100) NOT NULL,
					`UPDATED` VARCHAR(11) NOT NULL,
					`TIMESTAMP` DATETIME NOT NULL,
					PRIMARY KEY (`SYNC_ID`))
				CHARACTER SET utf8 COLLATE utf8_general_ci;";
		$this->_db->execute($sql);
	}
	
}
?>