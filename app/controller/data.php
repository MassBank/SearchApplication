<?php

require_once APP . '/entity/constant/file/keyword.php';
require_once APP . '/entity/constant/db/column.php';
require_once APP . '/model/base/data_model.php';
require_once APP . '/model/db/sync_info_model.php';
require_once APP . '/model/file/file_model.php';
require_once APP . '/model/log/logfile.php';
require_once APP . '/model/util/background_process.php';

require_once APP . '/model/mq/mq_message.php';
require_once APP . '/model/mq/mq_queue.php';
require_once APP . '/controller/data.php';

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
	
	public function __construct() {
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
		$content_type = $this->get_header_value( "Content-Type" );
		
		if ( strcmp($content_type, "application/hal+json") == 0 ) 
		{
			// read request data content
			$this->log->info("[HEADER] post header: " . $content_type);
			$post_body = file_get_contents( 'php://input' );
			$body_json = json_decode( $post_body, TRUE );
			$this->log->info("[CONTENT] post body content: " . $post_body);
			// start data synchronization
			$sync_id = date('Ymdhis');
			
			$this->log->resource("[START] data synchronization (Sync-ID: " . $sync_id . ")");
			// event values
			$repository = $body_json["name"];
			$media_types = $body_json["media_types"];
			$updated = $body_json["updated"];
			$resources = $body_json["resources"];
			
			$index = 1;
			foreach ( $resources as $resource )
			{
				$key = round(microtime(true) * 1000) . $index;
				$data = array(
					'time' => time(),
					'key' => $key,
					'repository' => $repository,
					'resource' => $resource,
					'media_types' => $media_types,
					'updated' => $updated,
					'host-url' => URL
				);
				$this->log->info("[START] add message queue");
				MqQueue::addMessage($key, $data);
				$this->log->info("[END] add message queue");
				$index++;
			}
			// call shell script via PHP
			$this->log->info("[START] run monitor shell script");
			shell_exec( "/bin/sh " . ROOT . "public/monitor.sh" );
			$this->log->info("[END] run monitor shell script");
			
			$this->log->resource("[END] data synchronization (Sync-ID: " . $sync_id . ")");
		} else {
			$this->log->error("[HEADER] No valid json header value");
			$this->log->info("--- Request header list ---");
			foreach ( getallheaders() as $name => $value ) {
				$this->log->info("[HEADER] $name => $value");
			}
		}
		
		
// 		// call /sync URL
// 		$url = 'http://git.localhost/webhook/event.json';
// 		$obj = json_decode( file_get_contents($url), true );
		

		/* 
		
 		if ( empty($this->_sync_info_model) ) {
 			$this->_sync_info_model = new Sync_Info_Model();
 			$this->_sync_info_model->create_table_if_not_exists();
 		}
 
		// save response urls into text file
		foreach ( $obj["resources"] as $resource ) {
			$timestamp = date('Y-m-d H:i:s');
			$this->log->resource("[RECEIVED] sync details (S-ID: " . $sync_id . ") --> (resource): " . $resource . ", (media_type): " . $media_type . ", (updated): " . $updated);
			$this->_sync_info_model->insert($repository, $resource, $media_type, $updated, $timestamp);
		}
		if ( !isset($GLOBALS['process']) || !$GLOBALS['process']->isRunning() ) {
			$GLOBALS['process'] = new BackgroundProcess("wget --header='Content-Type: application/hal+json' " . $this->_url_data_merge);
			$GLOBALS['process']->run();
			$this->log->resource("[RUN] background process (PID: " . $GLOBALS['process']->getPid() . ") for merge"); // PID create after start process
		} else {
			$this->log->resource("[ON GOING] background process (PID: " . $GLOBALS['process']->getPid() . ") for merge");
		} 
		
		*/
		
	}
	
	public function merge_resource() {
		$resource_url = $_GET["resource"];
		$media_types = $_GET["media_types"];
		$this->merge_url_data($resource_url, $media_types);
	}

// 	public function merge_resource_data()
// 	{
// 		$merge_id = date('Ymdhis');
// 		$this->log->resource("[START] merge resource data (M-ID: " . $merge_id . ")");
// 		$this->_sync_info_model = new Sync_Info_Model();
		
// 		$do_loop = true;
// 		$loop_limit = 5;
// 		$loop_index = 0;
		
// 		$pagination = new Pagination_Param();
// 		$pagination->set_limit( $loop_limit );
// 		$pagination->set_order( Column::SYNC_TIMESTAMP );
// 		$pagination->set_sort( "ASC" );
		
// 		while ( $do_loop ) {
// 			// create pagination
// 			$pagination->set_start( $loop_index );
// 			// get resource url list
// 			$sync_info_list = $this->_sync_info_model->get_sync_info_list( $pagination );
			
// 			foreach ( $sync_info_list as $sync_info ) {
// 				$this->log->resource("[MERGE] resource Url: " . $sync_info["RESOURCE"] . " (M-ID: " . $merge_id . ")");
// 				$this->merge_url_data( $sync_info["RESOURCE"] );
// 				// remove merged resource url
// 				$this->log->resource("[DELETE] resource Url: " . $sync_info["RESOURCE"] . " (M-ID: " . $merge_id . ")");
// 				$this->_sync_info_model->delete_sync_info( $sync_info["SYNC_ID"] );
// 			}
			
// 			if ( sizeof( $sync_info_list ) == 0 ) {
// 				$do_loop = false;
// 			}
// 			$loop_index++;
// 		}
// 		$this->log->resource("[END] merge resource data (M-ID: " . $merge_id . ")");
// 	}
	
	private function merge_url_data($external_file_url, $media_types = NULL)
	{
		$this->log->info("[START SYNC] merge url data : " . $external_file_url);
		$parts = explode('/', $external_file_url);
		if ( sizeof($parts) > 0 )
		{
			$file_name = end($parts);
			$download_file_path = ROOT . "tmp/" . $file_name . "." . date("YmdHis");
			
			$this->log->info("[READ] sync data media_types: " . var_export($media_types, true));
			$sb_media_type = new String_Builder();
			foreach ( $media_types as $media_type ) {
				$sb_media_type->append($media_type . ",");
			}
			$headers = array( 'Accept: ' . rtrim($sb_media_type->to_string(), ",") );
			
			$file_model = new File_Model();
			$this->log->info("[DOWNLOAD] sync url data : (external) " . $external_file_url . " as (internal) " . $download_file_path);
			$file_model->download_url_data($external_file_url, $headers, $download_file_path);
			$this->log->info("[MERGE] sync downloaded file : " . $download_file_path);
			$file_model->merge_msp_data($download_file_path);
			$this->log->info("[REMOVE] sync downloaded file : " . $download_file_path);
			$file_model->remove_file($download_file_path); 
		}
		$this->log->info("[END SYNC] merge url data : " . $external_file_url);
	}
	
	private function get_header_value($field)
	{
// 		$this->log->info("header: $name => $value");
		foreach ( getallheaders() as $name => $value ) {
			if ( strcmp($field, $name) == 0 ) {
				return $value;
			}
		}
		return NULL;
	}

}
?>