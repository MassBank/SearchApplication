<?php
class View {
	
	public function render($path, $data = false, $error = false){
		require APP . "/view/$path.php";
	}
	
	public function rendertemplate($path, $data = false){
		require APP . "/template/".Session::get('template')."/$path.php";
	}
	
}
?>