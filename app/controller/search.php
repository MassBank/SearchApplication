<?php
class Search extends Controller
{
	public function index()
	{
		echo "search page";
		echo '-----';
		print_r($this->GET('page'));
		echo '-----';
		echo $this->GET('type');
		echo '-----';
	}
	
	public function quick()
	{
		echo "quick search page";
	}
}
?>