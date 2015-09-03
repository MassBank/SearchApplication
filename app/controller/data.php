<?php

require_once APP . '/entity/constant/file/keyword.php';
require_once APP . '/entity/constant/db/column.php';
require_once APP . '/model/base/data_model.php';
require_once APP . '/model/db/sync_info_model.php';
require_once APP . '/model/file/file_model.php';
require_once APP . '/model/log/logfile.php';
require_once APP . '/model/log/log4massbank.php';
require_once APP . '/model/util/background_process.php';

class Data extends Controller
{

	private $sync_file_count = 20;
	private $_instrument_model;
	private $_ms_model;
	private $_compound_model;
	private $_compound_name_model;
	private $_peak_model;
	private $_sync_info_model;
	
	private $_resource_log;
	private $log;
	
	private $_url_data_merge = 'http://git.localhost/mbsearchapi/data/merge';
	
	public function __construct(){
		parent::__construct();
		$this->log = new Log4Massbank();
	}
	
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

	public function sync() 
	{
		$sync_id = date('Ymdhis');
		$this->log->resource("[START] data synchronization (S-ID: " . $sync_id . ")");
		
		if ( empty($this->_sync_info_model) ) {
			$this->_sync_info_model = new Sync_Info_Model();
			$this->_sync_info_model->create_table_if_not_exists();
		}
		
// 		$prefix = "webhook-" . date('Ymdhis');
// 		$this->_resource_log = new LogFile($prefix); 
		
		// call /sync URL
		$url = 'http://git.localhost/webhook/event.json';
		$obj = json_decode( file_get_contents($url), true );
		
		// event values
		$repository = $obj["name"];
		$media_type = $obj["media_types"][0];
		$updated = $obj["updated"];

		// save response urls into text file
		foreach ( $obj["resources"] as $resource ) {
			$timestamp = date('Y-m-d H:i:s');
			$this->log->resource("[RECEIVED] sync details (S-ID: " . $sync_id . ") --> (resource): " . $resource . ", (media_type): " . $media_type . ", (updated): " . $updated);
			$this->_sync_info_model->insert($repository, $resource, $media_type, $updated, $timestamp);
		}
		// call shell script via PHP
// 		shell_exec( "/bin/sh " . ROOT . "public/run.sh" );
		if ( !isset($GLOBALS['process']) || !$GLOBALS['process']->isRunning() ) {
			$GLOBALS['process'] = new BackgroundProcess("wget --header='Content-Type: application/hal+json' " . $this->_url_data_merge);
			$GLOBALS['process']->run();
			$this->log->resource("[RUN] background process (PID: " . $GLOBALS['process']->getPid() . ") for merge"); // PID create after start process
		} else {
			$this->log->resource("[ON GOING] background process (PID: " . $GLOBALS['process']->getPid() . ") for merge");
		}
		
		$this->log->resource("[END] data synchronization (S-ID: " . $sync_id . ")");
	}

	public function merge_resource_data()
	{
		$merge_id = date('Ymdhis');
		$this->log->resource("[START] merge resource data (M-ID: " . $merge_id . ")");
		$this->_sync_info_model = new Sync_Info_Model();
		
		$do_loop = true;
		$loop_limit = 5;
		$loop_index = 0;
		
		$pagination = new Pagination_Param();
		$pagination->set_limit( $loop_limit );
		$pagination->set_order( Column::SYNC_TIMESTAMP );
		$pagination->set_sort( "ASC" );
		
		while ( $do_loop ) {
			// create pagination
			$pagination->set_start( $loop_index );
			// get resource url list
			$sync_info_list = $this->_sync_info_model->get_sync_info_list( $pagination );
			
			foreach ( $sync_info_list as $sync_info ) {
				$this->log->resource("[MERGE] resource Url: " . $sync_info["RESOURCE"] . " (M-ID: " . $merge_id . ")");
				$this->merge_url_data( $sync_info["RESOURCE"] );
				// remove merged resource url
				$this->log->resource("[DELETE] resource Url: " . $sync_info["RESOURCE"] . " (M-ID: " . $merge_id . ")");
				$this->_sync_info_model->delete_sync_info( $sync_info["SYNC_ID"] );
			}
			
			if ( sizeof( $sync_info_list ) == 0 ) {
				$do_loop = false;
			}
			$loop_index++;
		}
		$this->log->resource("[END] merge resource data (M-ID: " . $merge_id . ")");
	}
	
	private function merge_url_data($external_file_url)
	{
		$this->log->info("SYNC START merge url data : " . $external_file_url);
		$parts = explode('/', $external_file_url);
		if ( sizeof($parts) > 0 )
		{
			$file_name = end($parts);
			$internal_file_path = ROOT . "tmp/" . $file_name . "." . date("YmdHis");
				
			$file_model = new File_Model();
			$this->log->info("DOWNLOAD SYNC url data : (external) " . $external_file_url . " as (internal) " . $internal_file_path);
			$file_model->download_external_file($external_file_url, $internal_file_path);
			$this->log->info("MERGE SYNC data from (external) " . $external_file_url);
			$file_model->merge_msp_data($internal_file_path);
			$this->log->info("REMOVE SYNC downloaded file : (internal) " . $internal_file_path);
			$file_model->remove_file($internal_file_path);
		}
		$this->log->info("SYNC END merge url data : " . $external_file_url);
	}

}
?>