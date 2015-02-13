<?php

class String_Builder {
	var $_string = '';

	/**
	 * Appends this string onto the end of the full string
	 * @param string $string
	 */
	public function append($string) {
		$this->_string .= $string;
	}

	/**
	 * Returns the full string
	 * @param none
	 * @return the fully appended string
	 */
	public function to_string() {
		return $this->_string;
	}
}

?>