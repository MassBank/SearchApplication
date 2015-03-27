<?php
require_once APP . '/model/util/string_builder.php';

class Product_Ion_Model extends Model
{
	const TABLE = "product_ion";
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_compound_ids_intersect_by_formulas($formula_list)
	{
		$num = sizeof( $formula_list );
		if ( $num > 0 )
		{
			$sb_sql = new String_Builder();
			$sb_whr = new String_Builder();
			$i = 0;
			$sb_sql->append( "SELECT t0.COMPOUND_ID FROM " );
			foreach ( $formula_list as $formula )
			{
				if ( !empty($formula) )
				{
					$sb_sql->append( "(SELECT COMPOUND_ID FROM " . self::TABLE . " WHERE FORMULA='" . $formula_list[$i] . "') AS t" . $i );
				}
				if ( $i > 0 )
				{
					$sb_whr->append( "t" . ($i - 1) . ".COMPOUND_ID = t" . $i . ".COMPOUND_ID" );
					if ( $num > 1 && $i < $num - 1 ) {
						$sb_whr->append( " AND " );
					}
				}
				if ( $num > 1 && $i < $num - 1 )
				{
					$sb_sql->append( ", " );
				}
				$i++;
			}
			$where = $sb_whr->to_string();
			if ( !empty($where) ) {
				$sb_sql->append( " WHERE " . $where );
			}
			$sql = $sb_sql->to_string();
// 			echo $sql;
			return $this->_db->list_result($sql);
		}
		return array();
	}
	
	public function get_compound_ids_in_formulas($formula_list)
	{
		$num = sizeof( $formula_list );
		if ( $num > 0 )
		{
			$sql = "SELECT COMPOUND_ID FROM " . self::TABLE . " WHERE FORMULA IN ('" . implode("','", $formula_list) . "')";
			// echo $sql;
			return $this->_db->list_result($sql);
		}
		return array();
	}
	
}
?>