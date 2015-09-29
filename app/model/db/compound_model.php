<?php

require_once APP . '/model/util/string_builder.php';
require_once APP . '/model/db/compound_name_model.php';

class Compound_Model extends Model
{
	
	const TABLE = "compound";
	
	private $log;
	
	public function __construct()
	{
		parent::__construct();
		$this->log = new Log4Massbank();
	}
	
	public function get_compounds_by_keywords_and_ids($compound_ids,
			$keyword_terms, $formula_term, $min_mol_mass, $max_mol_mass,
			$ion_mode, $instrument_ids, $ms_type_ids, $pagination = NULL, $is_count = FALSE)
	{
		$op1 = "AND";
		$op2 = "AND";
		
		$sb_compound_sql = new String_Builder();
		if ( $is_count || empty($pagination) ) {
			$sb_compound_sql->append("SELECT COUNT(DISTINCT C.COMPOUND_ID) AS HIT_COUNT FROM ");
		} else {
			$sb_compound_sql->append("SELECT DISTINCT C.COMPOUND_ID, C.TITLE, C.ION_MODE, C.FORMULA, C.PUBCHEM_ID, C.PUBCHEM_ID_TYPE, C.EXACT_MASS FROM ");
		}
		$sb_compound_sql->append(Compound_Name_Model::TABLE . " CN LEFT JOIN " . self::TABLE . " C ON CN.COMPOUND_ID = C.COMPOUND_ID");
		
		$where_clause = array();
		$where_clause2 = array();
		
		// ion_mode
		if ( $ion_mode == 1 ) {
			array_push($where_clause, "C.ION_MODE > 0");
		} else if ( $ion_mode == -1 ) {
			array_push($where_clause, "C.ION_MODE < 0");
		}
		// instruments
		if ( !empty($instrument_ids) ) {
			array_push($where_clause, "C.INSTRUMENT_ID IN(" . implode(",", $instrument_ids) . ")");
		}
		// ms
		if ( !empty($ms_type_ids) ) {
			array_push($where_clause, "C.MS_TYPE_ID IN(" . implode(",", $ms_type_ids) . ")");
		}
		// compound ids
		if ( !empty($compound_ids) ) {
			array_push($where_clause, "C.COMPOUND_ID IN(" . implode(",", $compound_ids) . ")");
		}
		// compound name
		if ( !empty($keyword_terms) ) {
			$i = 0;
			array_push($where_clause2, "(");
			foreach ($keyword_terms as $keyword_term) {
				$keyword_term = str_replace("'", "''", $keyword_term);
				if ($i > 0) {
					array_push($where_clause2, " || ");
				}
				array_push($where_clause2, "CN.NAME LIKE '%" . $keyword_term . "%'");
				array_push($where_clause2, " || C.TITLE LIKE '%" . $keyword_term . "%'");
				array_push($where_clause2, " || C.AUTHORS LIKE '%" . $keyword_term . "%'");
				array_push($where_clause2, " || C.LICENSE LIKE '%" . $keyword_term . "%'");
				array_push($where_clause2, " || C.INSTRUMENT LIKE '%" . $keyword_term . "%'");
				array_push($where_clause2, " || C.FORMULA LIKE '%" . $keyword_term . "%'");
				array_push($where_clause2, " || C.COMPOUND_ID LIKE '%" . $keyword_term . "%'");
				$i++;
			}
			array_push($where_clause2, ")");
		}
		// min & max exact mass
		if ( $min_mol_mass && $max_mol_mass ) {
			array_push($where_clause2, " " . $op1 . " ");
			array_push($where_clause2, "(C.EXACT_MASS BETWEEN " . $min_mol_mass . " AND " . $max_mol_mass . ")");
		}
		// formula
		if ( $formula_term ) {
			array_push($where_clause2, " " . $op2 . " ");
			array_push($where_clause2, "C.FORMULA LIKE '%" . $formula_term . "%'");
		}
		
		$str_where_clause2 = implode("", $where_clause2);
		if ( Common_Util::startwith($str_where_clause2, " " . $op1 . " ") ) {
			$str_where_clause2 = Common_Util::first_str_replace($str_where_clause2, " " . $op1 . " ", "");
		}
		else if ( Common_Util::startwith($str_where_clause2, " " . $op2 . " ") ) {
			$str_where_clause2 = Common_Util::first_str_replace($str_where_clause2, " " . $op2 . " ", "");
		}
		
		if ( !empty($str_where_clause2) ) {
			array_push($where_clause, "(" . $str_where_clause2 . ")");
		}
		
		if ( !empty($where_clause) ) {
			$sb_compound_sql->append(" WHERE ");
			$sb_compound_sql->append(implode(" AND ", $where_clause));
		}
		
		$this->append_pagination_clause($sb_compound_sql, $pagination);
		
		$sql = $sb_compound_sql->to_string();
		$this->log->debug($sql);
		return $this->_db->list_result($sql);
	}
	
	public function get_compounds_by_keywords(
			$compound_name_term, $formula_term, $min_mz, $max_mz, $op1, $op2,
			$ion_mode, $instrument_ids, $ms_type_ids, $pagination, $is_count = FALSE)
	{
		$sb_compound_sql = new String_Builder();
		if ($is_count) {
			$sb_compound_sql->append("SELECT COUNT(DISTINCT C.COMPOUND_ID) AS HIT_COUNT FROM ");
		} else {
			$sb_compound_sql->append("SELECT DISTINCT C.COMPOUND_ID, C.TITLE, C.ION_MODE, C.FORMULA, C.PUBCHEM_ID, C.PUBCHEM_ID_TYPE, C.EXACT_MASS FROM ");
		}
		$sb_compound_sql->append(Compound_Name_Model::TABLE . " CN LEFT JOIN " . self::TABLE . " C ON CN.COMPOUND_ID = C.COMPOUND_ID");
	
		$where_clause = array();
		$where_clause2 = array();
	
		// ion_mode
		if ( $ion_mode == 1 ) {
			array_push($where_clause, "C.ION_MODE > 0");
		} else if ( $ion_mode == -1 ) {
			array_push($where_clause, "C.ION_MODE < 0");
		}
		// instruments
		if ( !empty($instrument_ids) ) {
			array_push($where_clause, "C.INSTRUMENT_ID IN(" . implode(",", $instrument_ids) . ")");
		}
		// ms
		if ( !empty($ms_type_ids) ) {
			array_push($where_clause, "C.MS_TYPE_ID IN(" . implode(",", $ms_type_ids) . ")");
		}
		// compound name
		if ( !empty($compound_name_term) ) {
			$compound_name_term = str_replace("'", "''", $compound_name_term);
			array_push($where_clause2, "CN.NAME LIKE '%" . $compound_name_term . "%'");
		}
		// min & max exact mass
		if ( $min_mz && $max_mz ) {
			array_push($where_clause2, " " . $op1 . " ");
			array_push($where_clause2, "(C.EXACT_MASS BETWEEN " . $min_mz . " AND " . $max_mz . ")");
		}
		// formula
		if ( $formula_term ) {
			array_push($where_clause2, " " . $op2 . " ");
			array_push($where_clause2, "C.FORMULA LIKE '%" . $formula_term . "%'");
		}
	
		$str_where_clause2 = implode("", $where_clause2);
		if ( Common_Util::startwith($str_where_clause2, " " . $op1 . " ") ) {
			$str_where_clause2 = Common_Util::first_str_replace($str_where_clause2, " " . $op1 . " ", "");
		}
		else if ( Common_Util::startwith($str_where_clause2, " " . $op2 . " ") ) {
			$str_where_clause2 = Common_Util::first_str_replace($str_where_clause2, " " . $op2 . " ", "");
		}

		if ( !empty($str_where_clause2) ) {
			array_push($where_clause, "(" . $str_where_clause2 . ")");
		}

		if ( !empty($where_clause) ) {
			$sb_compound_sql->append(" WHERE ");
			$sb_compound_sql->append(implode(" AND ", $where_clause));
		}
		
		if ( !empty($pagination) ) {
			$this->append_pagination_clause($sb_compound_sql, $pagination);
		}
	
		$sql = $sb_compound_sql->to_string();
		$this->log->debug($sql);
		return $this->_db->list_result($sql);
	}
	
	public function count_compounds_by_keywords(
			$compound_name_term, $formula_term, $min_mz, $max_mz, $op1, $op2,
			$ion_mode, $instrument_ids, $ms_type_ids)
	{
		$result = $this->get_compounds_by_keywords($compound_name_term, $formula_term, $min_mz, $max_mz, $op1, $op2, 
				$ion_mode, $instrument_ids, $ms_type_ids, NULL, TRUE);
		if ( !empty($result) ) {
			return $result[0]['HIT_COUNT'];
		}
		return 0;
	}
	
	public function get_compound_by_id($compound_id)
	{
		$sql = "SELECT * FROM " . self::TABLE . " C WHERE C." . Column::COMPOUND_ID . " = :compound_id";
		$params = array(
			":compound_id" => $compound_id
		);
		return $this->_db->unique_result($sql, $params);
	}
	
	public function get_compounds_by_ids($compound_ids, $pagination)
	{
		if ( !empty($compound_ids) )
		{
			$sb_compound_sql = new String_Builder();
			$sb_compound_sql->append("SELECT * FROM " . self::TABLE . " C");
			$sb_compound_sql->append(" WHERE C." . Column::COMPOUND_ID . " IN('" . implode("','", $compound_ids) . "')");
			
			$this->append_pagination_clause($sb_compound_sql, $pagination);
			$sql = $this->_get_formatted_sql($sb_compound_sql);
			$this->log->debug($sql);
			return $this->_db->list_result($sql);
		}
		else {
			$this->log->warning("empty compound ids");
			return array();
		}
	}

	public function get_compounds_by_ids2($compound_ids, $ion_mode, $instrument_ids = array(), $ms_type_ids = array(), $pagination, $is_count = FALSE)
	{
		if ( !empty($compound_ids) )
		{
			$sb_compound_sql = new String_Builder();
			if ( $is_count ) {
				$sb_compound_sql->append("SELECT COUNT(C." . Column::COMPOUND_ID . ") AS HIT_COUNT FROM " . self::TABLE . " C");
			} else {
				$sb_compound_sql->append("SELECT * FROM " . self::TABLE . " C");
			}
			$sb_compound_sql->append(" WHERE C." . Column::COMPOUND_ID . " IN('" . implode("','", $compound_ids) . "')");
			// ion_mode
			if ( $ion_mode == 1 ) {
				$sb_compound_sql->append(" C.ION_MODE > 0");
			} else if ( $ion_mode == -1 ) {
				$sb_compound_sql->append(" C.ION_MODE < 0");
			}
			// instruments
			if ( !empty($instrument_ids) ) {
				$sb_compound_sql->append(" C.INSTRUMENT_ID IN(" . implode(",", $instrument_ids) . ")");
			}
			// ms_types
			if ( !empty($ms_type_ids) ) {
				$sb_compound_sql->append(" C.MS_TYPE_ID IN(" . implode(",", $ms_type_ids) . ")");
			}
			
			$this->append_pagination_clause($sb_compound_sql, $pagination);
			
			$sql = $this->_get_formatted_sql($sb_compound_sql);
			$this->log->debug($sql);
			return $this->_db->list_result($sql);
		}
		else {
			$this->log->warning("empty compound ids");
			return array();
		}
	}
	
	public function count_compounds_by_ids2($compound_ids, $ion_mode, $instrument_ids = array(), $ms_type_ids = array())
	{
		$result = $this->get_compounds_by_ids2($compound_ids, $ion_mode, $instrument_ids, $ms_type_ids, NULL, TRUE);
		if ( !empty($result) ) {
			return $result[0]['HIT_COUNT'];
		}
		return 0;
	}
	
	public function get_compounds_by_ion_mode($ion_mode, $instrument_ids = array(), $ms_type_ids = array())
	{
		$sb_compound_sql = new String_Builder();
		$sb_compound_sql->append("SELECT * FROM " . self::TABLE . " C WHERE");
		
		// ion_mode
		if ( $ion_mode == 1 ) {
			$sb_compound_sql->append(" C.ION_MODE > 0");
		} else if ( $ion_mode == -1 ) {
			$sb_compound_sql->append(" C.ION_MODE < 0");
		}
		// instruments
		if ( !empty($instrument_ids) ) {
			$sb_compound_sql->append(" C.INSTRUMENT_ID IN(" . implode(",", $instrument_ids) . ")");
		}
		// ms_types
		if ( !empty($ms_type_ids) ) {
			$sb_compound_sql->append(" C.MS_TYPE_ID IN(" . implode(",", $ms_type_ids) . ")");
		}
		// TODO: MS$FOCUSED_ION: PRECURSOR_MZ
	
// 		$sb_compound_sql->append(" ORDER BY C.COMPOUND_ID");
	
		$sql = $this->_get_formatted_sql($sb_compound_sql);
		$sql .= " ORDER BY C.COMPOUND_ID";
		$this->log->debug($sql);
		return $this->_db->list_result($sql);
	}
	
	// manipulation query
	
	public function delete_all()
	{
// 		$sql = "TRUNCATE TABLE `" . Compound_Model::TABLE . "`"; // very quickly than DELETE FROM TABLE
		$sql = "DELETE FROM `" . Compound_Model::TABLE . "`"; // very quickly than DELETE FROM TABLE
		$this->_db->execute($sql);
	}
	
	public function drop_table()
	{
		$sql = "DROP TABLE `" . Compound_Model::TABLE . "`";
// 		$sql = "SET FOREIGN_KEY_CHECKS=0;DROP TABLE `" . Compound_Model::TABLE . "`; SET FOREIGN_KEY_CHECKS=1;";
		$this->_db->execute($sql);
	}
	
	public function create_table_if_not_exists()
	{
		$sql = "CREATE TABLE IF NOT EXISTS `" . Compound_Model::TABLE . "` (
					`COMPOUND_ID` VARCHAR(10) NOT NULL,
					`TITLE` VARCHAR(255) DEFAULT NULL,	
					`AUTHORS` VARCHAR(255) DEFAULT NULL,	
					`INSTRUMENT` VARCHAR(255) DEFAULT NULL,
					`LICENSE` VARCHAR(255) DEFAULT NULL,		
					`FORMULA` VARCHAR(255) DEFAULT NULL,
					`EXACT_MASS` FLOAT(10,5) DEFAULT NULL,
					`ION_MODE` TINYINT DEFAULT NULL,
					`PUBCHEM_ID` varchar(255) DEFAULT NULL,
  					`PUBCHEM_ID_TYPE` varchar(100) DEFAULT NULL,
					`MS_TYPE_ID` INT(5) DEFAULT NULL,
					`INSTRUMENT_ID` INT(11) DEFAULT NULL,
					`CREATE_DATE` datetime DEFAULT NULL,
  					`UPDATE_DATE` datetime DEFAULT NULL,
					PRIMARY KEY (`COMPOUND_ID`),
					KEY `FK_IDX_COMPOUND_MSTYPE` (`MS_TYPE_ID`),
					KEY `FK_IDX_COMPOUND_INSTRUMENT` (`INSTRUMENT_ID`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$this->_db->execute($sql);
	}
	
	public function merge($compound_id, $title, $authors, $instrument, $license, $formula, $exact_mass, $ion_mode, 
			$pubchem_id, $pubchem_id_type, $instrument_id, $ms_type_id, $create_date, $update_date)
	{
		$compound = $this->get_compound_by_id($compound_id);
		if ($compound != NULL) {
			$this->log->info( "[UPDATE] Compound Info: " . $compound_id );
			$this->update($compound_id, $title, $authors, $instrument, $license, $formula, $exact_mass, $ion_mode, 
					$pubchem_id, $pubchem_id_type, $instrument_id, $ms_type_id, $update_date);
		} else {
			$this->log->info( "[INSERT] Compound Info: " . $compound_id );
			$this->insert($compound_id, $title, $authors, $instrument, $license, $formula, $exact_mass, $ion_mode, 
					$pubchem_id, $pubchem_id_type, $instrument_id, $ms_type_id, $create_date, $update_date);
		}
	}
	
	public function update($compound_id, $title, $authors, $instrument, $license, $formula, $exact_mass, $ion_mode, 
			$pubchem_id, $pubchem_id_type, $instrument_id, $ms_type_id, $update_date)
	{
		$sql = "UPDATE " . Compound_Model::TABLE . " 
				SET TITLE=:title, AUTHORS=:authors, INSTRUMENT=:instrument, LICENSE=:license, FORMULA=:formula, EXACT_MASS=:exact_mass, 
				ION_MODE=:ion_mode, PUBCHEM_ID=:pubchem_id, PUBCHEM_ID_TYPE=:pubchem_id_type, INSTRUMENT_ID=:instrument_id, MS_TYPE_ID=:ms_type_id,
				UPDATE_DATE=:update_date
				WHERE COMPOUND_ID =:compound_id";
		$parameters = array(
				':compound_id' => $compound_id,
				':title' => $title,
				':authors' => $authors,
				':instrument' => $instrument,
				':license' => $license,
				':formula' => $formula,
				':exact_mass' => $exact_mass,
				':ion_mode' => $ion_mode,
				':pubchem_id' => $pubchem_id,
				':pubchem_id_type' => $pubchem_id_type,
				':instrument_id' => $instrument_id,
				':ms_type_id' => $ms_type_id,
				':update_date' => $update_date
		);
		$this->_db->execute($sql, $parameters);
	}
	
	public function insert($compound_id, $title, $authors, $instrument, $license, $formula, $exact_mass, $ion_mode, 
			$pubchem_id, $pubchem_id_type, $instrument_id, $ms_type_id, $create_date, $update_date)
	{
		$sql = "INSERT INTO " . Compound_Model::TABLE . " (
				COMPOUND_ID, TITLE, AUTHORS, INSTRUMENT, LICENSE, FORMULA, EXACT_MASS, ION_MODE, PUBCHEM_ID, PUBCHEM_ID_TYPE, 
				INSTRUMENT_ID, MS_TYPE_ID, CREATE_DATE, UPDATE_DATE
				) VALUES (
				:compound_id, :title, :authors, :instrument, :license, :formula, :exact_mass, :ion_mode, :pubchem_id, :pubchem_id_type, 
				:instrument_id, :ms_type_id, :create_date, :update_date)";
		$parameters = array(
				':compound_id' => $compound_id, 
				':title' => $title, 
				':authors' => $authors,
				':instrument' => $instrument,
				':license' => $license,
				':formula' => $formula, 
				':exact_mass' => $exact_mass, 
				':ion_mode' => $ion_mode,
				':pubchem_id' => $pubchem_id,
				':pubchem_id_type' => $pubchem_id_type,
				':instrument_id' => $instrument_id,
				':ms_type_id' => $ms_type_id,
				':create_date' => $create_date,
				':update_date' => $update_date
		);
		$this->_db->execute($sql, $parameters);
	}
	
// 	private function append_pagination_clause($sb_compound_sql, $pagination)
// 	{
// 		if ( !empty($pagination) ) 
// 		{
// 			$order_column = $pagination->get_order();
		
// 			if ( !empty($order_column) ) {
// 				$sb_compound_sql->append(" ORDER BY C." . strtoupper($order_column));
// 				$sort = $pagination->get_sort();
// 				if ( !empty($sort) ) {
// 					$sb_compound_sql->append(" " . strtoupper($sort));
// 				}
// 			}
		
// 			$start = $pagination->get_start();
// 			$num = $pagination->get_limit();
// 			if ( $start >= 0 && $num > 0 ) {
// 				$sb_compound_sql->append(" LIMIT " . $start . ", " . $num);
// 			}
			
// 		}
// 	}
	
}

?>