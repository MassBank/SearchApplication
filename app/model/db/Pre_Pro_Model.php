<?php
class Pre_Pro_Model extends Model
{
	const TABLE = "pre_pro";
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_compound_neutral_loss_of_formulas($formula_list)
	{
		$num = sizeof( $formula_list );
		if ( $num > 0 )
		{
			$sql = "SELECT COMPOUND_ID, NEUTRAL_LOSS FROM " . self::TABLE . " WHERE NEUTRAL_LOSS IN('" . implode("','", $formula_list) . "') GROUP BY COMPOUND_ID, NEUTRAL_LOSS ORDER BY COMPOUND_ID, NEUTRAL_LOSS";
// 			echo $sql;
			return $this->_db->list_result($sql);
		}
		return array();
	}
	
}
?>