<?php

require_once APP. '/entity/constant/file/keyword.php';
require_once APP. '/entity/constant/db/column.php';
require_once APP. '/model/base/data_model.php';

class Data extends Controller
{

	private $sync_file_count = 20;
	
	private $_instrument_model;
	private $_ms_model;
	private $_compound_model;
	private $_compound_name_model;
	private $_peak_model;
	
	public function index()
	{
		$params = $this->parse_query_str();
		$dir_path = $this->GET_PARAM("dir", $params); // 'C:/Apps/Documents/Projects/proj-massbank/massbankrecord'
		$is_drop_tables = $this->GET_PARAM("drop_tables", $params);
		$is_delete_all = $this->GET_PARAM("delete_all", $params);
		$sync_file_count = $this->GET_PARAM("sync_file_count", $params);
		
		if ( empty($dir_path) ) {
			return;
		}
		
		$data_model = new Data_Model();
		
		if ( $is_drop_tables ) 
		{
			$data_model->drop_tables();
		}

		if ( $is_delete_all )
		{
			$data_model->delete_all();
		}
		
		$data_model->create_table_if_not_exists();
		$data_model->merge_file_data($dir_path, $sync_file_count);
	}

}

?>