<?php
class Common_Util
{
	public static function startwith($str, $pattern)
	{
		return (0 === strpos($str, $pattern));
		// return $pattern === "" || strrpos($str, $pattern, -strlen($str)) !== FALSE;
	}
	
	public static function endswith($str, $pattern) {
		// search forward starting from end minus needle length characters
		return $pattern === "" || strpos($str, $pattern, strlen($str) - strlen($pattern)) !== FALSE;
	}
	
	public static function first_str_replace($str, $pattern, $replace)
	{
		// preg_replace('/abc/', '123', $str, 1); also same. but slow.
		$pos = strpos($str, $pattern);
		
		if ($pos !== false) 
		{
			return substr_replace($str, $replace, $pos, strlen($pattern));
		}
		return $str;
	}
	
	public static function last_str_replace($str, $pattern, $replace)
	{
		$pos = strrpos($str, $pattern);
	
		if($pos !== false)
		{
			return substr_replace($str, $replace, $pos, strlen($pattern));
		}
		return $str;
	}
	
	public static function get_min_max_by_tolerance($mass, $tolerance)
	{
		$result = array();
		if ( $mass > 0 ) 
		{
			$tolerance = abs($tolerance); // get absolute value of tolerance. (2 or -2 => 2)
			$min_mass = $mass - $tolerance - 0.00001;
			$max_mass = $mass + $tolerance + 0.00001;
			$result[0] = $min_mass;
			$result[1] = $max_mass;
		}
		return $result;
	}
}
?>