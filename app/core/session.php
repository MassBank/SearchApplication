<?php
class Session
{
	public static function init()
	{
		// if no session exist, start the session
		if (session_id() == '') {
			session_start();
		}
	}
}
?>