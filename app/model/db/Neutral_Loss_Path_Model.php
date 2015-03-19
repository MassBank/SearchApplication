<?php
require_once APP . '/model/util/string_builder.php';

class Neutral_Loss_Path_Model extends Model
{
	const TABLE = "NEUTRAL_LOSS_PATH";
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_compound_ids_by_neutral_loss_path_of_formulas($formula_list)
	{
		$num = sizeof( $formula_list );
		if ( $num > 0 )
		{
			$sb_like = new String_Builder();
			$i = 0;
			foreach ( $formula_list as $formula )
			{
				if ( !empty($formula) )
				{
					$sb_like->append( "%>" . $formula . "-" );
				}
			}
			$sb_like->append( "%" );
			$sql = "SELECT DISTINCT COMPOUND_ID FROM " . self::TABLE . " WHERE PATH LIKE '" . $sb_like->to_string() . "'";
			// echo $sql;
			return $this->_db->list_result($sql);
		}
		return array();
	}
	
}
?>