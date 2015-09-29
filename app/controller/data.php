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
	private static $sync_status = "RUN";
	
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
	
	public function refresh()
	{
		$this->log->info("[START] refresh search api content");
		$params = $this->parse_query_str();
		
		$is_drop_tables = $this->GET_PARAM("drop_tables", $params);
		$is_delete_all = $this->GET_PARAM("delete_all", $params);
		
		$username = $this->GET_PARAM("username", $params);
		$password = $this->GET_PARAM("password", $params);

		if ( strcmp($username, DB_USER) == 0 && strcmp($password, DB_PASS) == 0 )
		{
			$data_model = new Data_Model();
	
			if ( $is_drop_tables ) 
			{
				$this->log->info("[START] recreate search api database");
				$data_model->drop_tables();
				$data_model->create_table_if_not_exists();
				$this->log->info("[END] recreate search api database");
			}
	
			if ( $is_delete_all )
			{
				$this->log->info("[START] delete search api all data");
				$data_model->delete_all();
				$this->log->info("[END] delete search api all data");
			}
		} else {
			$this->log->error("invalid username or password");
		}
		$this->log->info("[END] refresh search api content");
	}
	
	public function merge_dir_data()
	{
		$params = $this->parse_query_str();
		$dir_path = $this->GET_PARAM("dir", $params); // 'C:/Apps/Documents/Projects/proj-massbank/massbankrecord'
		$sync_file_count = $this->GET_PARAM("sync_file_count", $params);
		
		$username = $this->GET_PARAM("username", $params);
		$password = $this->GET_PARAM("password", $params);
		
		if ( empty($dir_path) ) {
			$this->log->error("invalid directory path");
			return;
		}
		
		if ( strcmp($username, DB_USER) == 0 && strcmp($password, DB_PASS) == 0 )
		{
			$data_model = new Data_Model();
			$data_model->create_table_if_not_exists();
			$data_model->merge_file_data($dir_path, $sync_file_count);
		} else {
			$this->log->error("invalid username or password");
		}
	}

	public function synctest()
	{
		$remote_url = "http://49.212.184.212/mnn-2/projects/rfmejia/massbank_ms2n_curated/resources";
		$this->sync_db_with_url($remote_url);
	}
	
	private function sync_db_with_url($remote_url)
	{
		$this->set_sync_status();
		
		$i = 0;
		if ( $this->is_run_status() )
		{
			$this->log->info("[START sync]");
			do {
				$remote_url = $this->read_publisher_body_content($remote_url);
				$this->run_resource_monitor_script();
				$do_loop = !empty($remote_url) && $this->is_run_status(); // do loop if has next url
				$i++;
			} while ($do_loop);
			$this->log->info("[END sync]");
		} else {
			$this->log->info("[STOP] sync process forcely stopped.");
		}
	}
	
	public function sync() 
	{
		$this->log->info("[START] sync");
		$post_body = file_get_contents( 'php://input' );
		$body_json = json_decode( $post_body, TRUE );
		$representations = $body_json["data"]["links"]["representations"];
		$index = 1;
		foreach ( $representations as $representation )
		{
			$key = round(microtime(true) * 1000) . $index;
			$media_type = $representation["media_type"];
			$hrefs = $representation["href"];
			foreach ( $hrefs as $href )
			{
				$this->add_resource_to_mq($key, $href, $media_type);
				$index++;
			}
		}
		$this->run_resource_monitor_script();
		$this->log->info("[END] sync");
		
// 		// TODO set same as in test
// 		$this->set_sync_status();
		
// 		if ( $this->is_run_status() )
// 		{
// 			$this->log->info("[START sync]");
			
// 			$content_type = $this->get_header_value( "Content-Type" );
			
// 			if ( strcmp($content_type, "application/json") == 0 || strcmp($content_type, "application/hal+json") == 0 ) 
// 			{
// 				// read request data content
// 				$this->log->info("[HEADER] post header: " . $content_type);
// 				$post_body = file_get_contents( 'php://input' );
// 				$body_json = json_decode( $post_body, TRUE );
// 				$this->log->info("[CONTENT] post body content: " . $post_body);
// 				// start data synchronization
// 				$sync_id = date('Ymdhis');
				
// 				$this->log->resource("[START] data synchronization (Sync-ID: " . $sync_id . ")");
// 				// event values
// 				$resources = $body_json["resources"];
// 				$media_types = $body_json["media_types"];
				
// 				$index = 1;
// 				foreach ( $resources as $resource )
// 				{
// 					$key = round(microtime(true) * 1000) . $index;
// 					$this->add_resource_to_mq($key, $resource, $media_types);
// 					$index++;
// 				}
// 				// call shell script via PHP
// 				$this->run_resource_monitor_script();
				
// 				$this->log->resource("[END] data synchronization (Sync-ID: " . $sync_id . ")");
// 			} else {
// 				$this->log->error("[HEADER] No valid json header value");
// 				$this->log->info("--- Request header list ---");
// 				foreach ( getallheaders() as $name => $value ) {
// 					$this->log->info("[HEADER] $name => $value");
// 				}
// 			}
			
			
// 	// 		// call /sync URL
// 	// 		$obj = json_decode( file_get_contents($url), true );
			
	
// 			/* 
			
// 	 		if ( empty($this->_sync_info_model) ) {
// 	 			$this->_sync_info_model = new Sync_Info_Model();
// 	 			$this->_sync_info_model->create_table_if_not_exists();
// 	 		}
	 
// 			// save response urls into text file
// 			foreach ( $obj["resources"] as $resource ) {
// 				$timestamp = date('Y-m-d H:i:s');
// 				$this->log->resource("[RECEIVED] sync details (S-ID: " . $sync_id . ") --> (resource): " . $resource . ", (media_type): " . $media_type . ", (updated): " . $updated);
// 				$this->_sync_info_model->insert($repository, $resource, $media_type, $updated, $timestamp);
// 			}
// 			if ( !isset($GLOBALS['process']) || !$GLOBALS['process']->isRunning() ) {
// 				$GLOBALS['process'] = new BackgroundProcess("wget --header='Content-Type: application/hal+json' " . $this->_url_data_merge);
// 				$GLOBALS['process']->run();
// 				$this->log->resource("[RUN] background process (PID: " . $GLOBALS['process']->getPid() . ") for merge"); // PID create after start process
// 			} else {
// 				$this->log->resource("[ON GOING] background process (PID: " . $GLOBALS['process']->getPid() . ") for merge");
// 			} 
			
// 			*/
// 			$this->log->info("[END sync]");
// 		} else {
// 			$this->log->info("[STOP] sync process forcely stopped.");
// 		}
	}
	
	public function download() {
		echo "START";
		$url = "http://49.212.184.212/mnn-2/revisions/ae314952-4af6-42f3-a866-03d90b46f152/resources/mt/f33a2d9751f4a10ea57b62f970f84e0e?offset=50";
		$parts = explode('/', $url);
		$file_name = end($parts);
		$download_file_path = ROOT . "tmp/" . $file_name . "." . date("YmdHis");
			
		$output = false;
		if ( function_exists('curl_init') ) {
			# open file to write
			$fp = fopen ($download_file_path, 'w+');
			# start curl
			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, $url );
			# set return transfer to false
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, false );
			curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
			# increase timeout to download big file
			curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
			# write data to local file
			curl_setopt( $ch, CURLOPT_FILE, $fp );
			# execute curl
			curl_exec( $ch );
			# close curl
			curl_close( $ch );
			# close local file
			fclose( $fp );
			
			if (filesize($download_file_path) > 0) return true;
			
			
			
			
// 			$fp = fopen ( $download_file_path, "w+" );
// 			$ch = curl_init();
// 			curl_setopt( $ch, CURLOPT_URL, $url );
// 			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
// 			curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
// 			//curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
// 			curl_setopt( $ch, CURLOPT_FILE, $fp );
// 			$output = curl_exec( $ch );
// 			curl_close( $ch );
// 			fclose( $fp );
		} else {
			$this->log->error("cURL is not installed");
		}
		echo "END";
		return $output;
	}
	
	public function merge_resource() {
		$resource_url = $_GET["resource"];
		$media_types = $_GET["media_types"];
		$this->merge_url_data($resource_url, $media_types);
	}
	
	// TEMP
	public function merge_temp() {
		$this->log->info("[START] merge temp folder");
		if ($handle = opendir(ROOT . "tmp/")) {
			$this->log->info("Directory handle: $handle\n");
		
			$file_model = new File_Model();
			/* This is the correct way to loop over the directory. */
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != "..") {
					$download_file_path = ROOT . "tmp/" . $entry;
					$this->log->info("[MERGE] sync downloaded file : " . $download_file_path);
					$file_model->merge_msp_data($download_file_path);
					$this->log->info("[REMOVE] sync downloaded file : " . $download_file_path);
					$file_model->remove_file($download_file_path);
				}
			}
		
			closedir($handle);
		}
		$this->log->info("[END] merge temp folder");
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

	private function set_sync_status() 
	{
		$params = $this->parse_query_str();
		$param_status = $this->GET_PARAM("status", $params);
		if ( isset($param_status) && !empty($param_status) ) {
			$this::$sync_status = strtoupper( $param_status );
		} else {
			$this::$sync_status = "RUN";
		}
	}
	
	private function is_run_status()
	{
		return strcmp($this::$sync_status, "RUN") == 0;
	}
	
	private function read_publisher_body_content($json_url)
	{
		$this->log->info("[READ] read publisher url: " . $json_url);
		$body_str = file_get_contents($json_url);
		$body_json = json_decode( $body_str, TRUE );
		
		$representations = $body_json["links"]["representations"];
		$index = 1;
		foreach ( $representations as $representation )
		{
			$key = round(microtime(true) * 1000) . $index;
			$this->add_resource_to_mq($key, $representation["href"], $representation["media_type"]);
			$index++;
		}
		
		$nxt_url = isset($body_json["links"]["next"]) ? $body_json["links"]["next"]: NULL;
		$this->log->info("[NEXT] read next publisher url: " . $nxt_url);
		return $nxt_url;
	}

	private function add_resource_to_mq($key, $resource, $media_types)
	{
		$this->log->info("[START] add message queue");
		if( !is_array($media_types) ) {
			$media_types = explode(" ", $media_types);
		}
		$data = array(
				'time' => time(),
				'key' => $key,
				'resource' => $resource,
				'media_types' => $media_types,
				'host-url' => URL
		);
		MqQueue::addMessage($key, $data);
		$this->log->info("[END] add message queue");
	}
	
	private function run_resource_monitor_script() 
	{
		// call shell script via PHP
		$this->log->info("[START] run monitor shell script");
		shell_exec( "/bin/sh " . ROOT . "public/monitor.sh" );
		$this->log->info("[END] run monitor shell script");
	}
	
	private function merge_url_data($external_file_url, $media_types = NULL)
	{
		$this->log->info("[START SYNC] merge url data : " . $external_file_url);
		$parts = explode('/', $external_file_url);
		if ( $this->is_run_status() && sizeof($parts) > 0 )
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
		foreach ( getallheaders() as $name => $value ) {
			if ( strcmp($field, $name) == 0 ) {
				return $value;
			}
		}
		return NULL;
	}

}
?>