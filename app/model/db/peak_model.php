<?php

class Peak_Model extends Model
{
	
	const TABLE = "peak";
	
	private $log;
	
	public function __construct()
	{
		parent::__construct();
		$this->log = new Log4Massbank();
	}
	
	public function get_high_intesity_peaks_by_range($min_mz, $max_mz, $rel_inte)
	{
		$sql = "SELECT " . Column::COMPOUND_ID . " FROM " . self::TABLE . 
				" WHERE (" . Column::PEAK_MZ . " BETWEEN :min_mz AND :max_mz) AND " . 
				Column::PEAK_RELATIVE_INTENSITY . " > :rel_inte";
		$params = array(
				':rel_inte' => $rel_inte,
				':min_mz' => $min_mz,
				':max_mz' => $max_mz
		);
		$this->log->debug($sql . ", min_mz:" . $min_mz . ", max_mz:" . $max_mz);
		return $this->_db->list_result($sql, $params);
	}
	
	public function get_high_intesity_peaks_diff_by_range($min_mz, $max_mz, $rel_inte)
	{
		$sb_sql = new String_Builder();
		$sb_sql->append("SELECT t1." . Column::COMPOUND_ID . " FROM " . self::TABLE . " AS t1 LEFT JOIN " . self::TABLE . " AS t2 ");
		$sb_sql->append("ON t2." . Column::COMPOUND_ID . " = t1." . Column::COMPOUND_ID . " ");
		$sb_sql->append("WHERE t1." . Column::PEAK_RELATIVE_INTENSITY . " > :rel_inte AND t2." . Column::PEAK_RELATIVE_INTENSITY . " > :rel_inte AND ");
		$sb_sql->append("(t1." . Column::PEAK_MZ . " BETWEEN t2." . Column::PEAK_MZ . " + :min_mz AND t2." . Column::PEAK_MZ . " + :max_mz)");
		$params = array(
				':rel_inte' => $rel_inte,
				':min_mz' => $min_mz,
				':max_mz' => $max_mz
		);
		$sql = $sb_sql->to_string();
		$this->log->debug($sql . ", min_mz:" . $min_mz . ", max_mz:" . $max_mz . ", rel_inte:" . $rel_inte);
		return $this->_db->list_result($sql, $params);
	}
	
// 	public function get_high_intesity_peaks_diff_by_range($min_mz, $max_mz, $rel_inte)
// 	{
// 		$sb_sql = new String_Builder();
// 		$sb_sql->append("SELECT t1." . Column::COMPOUND_ID . " FROM " . self::TABLE . " AS t1 LEFT JOIN " . self::TABLE . " AS t2 ");
// 		$sb_sql->append("ON t2." . Column::COMPOUND_ID . " = t1." . Column::COMPOUND_ID . " ");
// 		$sb_sql->append("WHERE ");
// 		$sb_sql->append("(t1." . Column::PEAK_MZ . " BETWEEN t2." . Column::PEAK_MZ . " + :min_mz AND t2." . Column::PEAK_MZ . " + :max_mz) ");
// 		$sb_sql->append("AND t1." . Column::PEAK_RELATIVE_INTENSITY . " > :rel_inte AND t2." . Column::PEAK_RELATIVE_INTENSITY . " > :rel_inte");
// 		$params = array(
// 				':rel_inte' => $rel_inte,
// 				':min_mz' => $min_mz,
// 				':max_mz' => $max_mz
// 		);
// 		$sql = $sb_sql->to_string();
// 		$this->log->debug($sql . ", min_mz:" . $min_mz . ", max_mz:" . $max_mz . ", rel_inte:" . $rel_inte);
// 		return $this->_db->list_result($sql, $params);
// 	}
	

	
	public function get_peaks_by_compound_id($compound_id, $mz_filters)
	{
		$num_of_mz = sizeof($mz_filters);
		$sb_whr = new String_Builder();
		$sql_where_clause = "";
		if ($num_of_mz > 0)
		{
			$sb_whr->append(" AND (");
			for ( $i = 0; $i < $num_of_mz; $i++ ) {
				$sb_whr->append("(MZ BETWEEN " . $mz_filters[$i]['min_mz'] . " AND " . $mz_filters[$i]['max_mz'] . " and RELATIVE_INTENSITY > " . $mz_filters[$i]['rel_int'] . ")");
				$sb_whr->append(" OR ");
			}
			$sb_whr->append(")");
			$sql_where_clause = $sb_whr->to_string();
			if (Common_Util::endswith($sql_where_clause, " OR ")) {
				Common_Util::last_str_replace($sql_where_clause, " OR ", "");
			}
		}
		$sql = "SELECT MZ FROM " . self::TABLE . " WHERE COMPOUND_ID=:compound_id" . $sql_where_clause;
		$params = array(
			':compound_id' => $compound_id
		);
		return $this->_db->list_result($sql, $params);
	}
	
	public function get_peak_differences_by_compound_id($compound_id, $rel_int, $min_mz, $max_mz)
	{
		$sql = "SELECT t2.MZ AS MZ2, t1.MZ AS MZ1 FROM " . self::TABLE . " AS t1 LEFT JOIN " . self::TABLE . " AS t2 ON t1.COMPOUND_ID = t2.COMPOUND_ID 
				WHERE t1.COMPOUND_ID = :compound_id AND (t1.MZ BETWEEN t2.MZ + :min_mz and t2.MZ + :max_mz) 
				AND t1.RELATIVE_INTENSITY > :rel_int and t2.RELATIVE_INTENSITY > :rel_int";
		$params = array(
			':compound_id' => $compound_id,
			':rel_int' => $rel_int,
			':min_mz' => $min_mz,
			':max_mz' => $max_mz
		);
		return $this->_db->list_result($sql, $params);
	}
	
	public function get_max_intensity_groups_by_compound($cutoff, $min_mz, $max_mz)
	{
		$sql = "SELECT MAX(CONCAT(LPAD(RELATIVE_INTENSITY, 3, ' '), ' ', COMPOUND_ID, ' ', MZ)) MAX_RELATIVE_INTENSITY FROM " . self::TABLE . " 
				WHERE RELATIVE_INTENSITY >= :rel_int and (MZ BETWEEN :min_mz AND :max_mz) GROUP BY COMPOUND_ID";
		$params = array(
			':rel_int' => floatval(sprintf("%d", $cutoff)),
			':min_mz' => floatval(sprintf("%.6f", $min_mz)),
			':max_mz' => floatval(sprintf("%.6f", $max_mz))
		);
		return $this->_db->list_result($sql, $params);
	}
	
	public function get_peaks_greater_than_cutoff($compound_id, $cutoff)
	{
		$sql = "SELECT MZ, RELATIVE_INTENSITY FROM " . self::TABLE . " WHERE COMPOUND_ID =:compound_id AND RELATIVE_INTENSITY >=:rel_int";
		$params = array(
			':compound_id' => strval(sprintf("%s", $compound_id)),
			':rel_int' => floatval(sprintf("%d", $cutoff))
		);
		return $this->_db->list_result($sql, $params);
	}
	
	public function delete_peaks_by_compound_id($compound_id)
	{
		$sql = "DELETE FROM `" . Peak_Model::TABLE . "` WHERE COMPOUND_ID =:compound_id";
		$parameters = array(
				':compound_id' => $compound_id
		);
		$this->_db->execute($sql, $parameters);
	}
	
	// manipulation query
	
	public function delete_all()
	{
// 		$sql = "TRUNCATE TABLE `" . Ms_Model::TABLE . "`"; // very quickly than DELETE FROM TABLE
		$sql = "DELETE FROM `" . Peak_Model::TABLE . "`"; // very quickly than DELETE FROM TABLE
		$this->_db->execute($sql);
	}
	
	public function drop_table()
	{
		$sql = "DROP TABLE `" . Peak_Model::TABLE . "`";
// 		$sql = "SET FOREIGN_KEY_CHECKS=0;DROP TABLE `" . Peak_Model::TABLE . "`; SET FOREIGN_KEY_CHECKS=1;";
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
					KEY `IDX_PEAK_MZ` (`MZ`),
				  	KEY `IDX_PEAK_RELATIVE_INTENSITY` (`RELATIVE_INTENSITY`),
				  	KEY `IDX_PEAK_COMPOUND` (`COMPOUND_ID`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
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
	
	public function bulk_insert($tbl_peaks, $compound_id) {
		$sql_sb = new String_Builder();
		$sql_sb->append( "INSERT INTO " . Peak_Model::TABLE . " (MZ, INTENSITY, RELATIVE_INTENSITY, COMPOUND_ID) VALUES " );
		foreach ($tbl_peaks as $tbl_peak) {
			$sql_sb->append( "(" );
			$sql_sb->append( $tbl_peak[Column::PEAK_MZ] . ", " );
			$sql_sb->append( $tbl_peak[Column::PEAK_INTENSITY] . ", " );
			$sql_sb->append( array_key_exists(Column::PEAK_RELATIVE_INTENSITY, $tbl_peak)? $tbl_peak[Column::PEAK_RELATIVE_INTENSITY]: 0 );
			$sql_sb->append( ", '" . $compound_id . "'" );
			$sql_sb->append( "), " );
		}
		$sql = rtrim(trim($sql_sb->to_string()), ",");
		$this->log->debug($sql);
		$this->_db->execute($sql);
	}
	
}
?>