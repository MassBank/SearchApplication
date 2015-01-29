<?php
class Common_Util
{
	public static function startwith($str, $pattern)
	{
		return (0 === strpos($str, $pattern));
	}
	
	public static function first_str_replace($str, $pattern, $replace)
	{
		// preg_replace('/abc/', '123', $str, 1); also same. but slow.
		$pos = strpos($str, $pattern);
		if ($pos !== false) {
			return substr_replace($str, $replace, $pos, strlen($pattern));
		}
		return $str;
	}
}
?>