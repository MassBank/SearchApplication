<?php

include_once APP . 'entity/constant/file/keyword.php';
include_once APP . 'entity/constant/db/column.php';

class File_Model extends Model
{
	
	private $_compound_model;
	private $_compound_name_model;
	private $_instrument_model;
	private $_ms_model;
	private $_peak_model;
	
	private $is_peak_lines = FALSE;
	private $max_inte = 0;
	private $log;
	
	public function __construct()
	{
		$this->_compound_model = $this->get_compound_model();
		$this->_compound_name_model = $this->get_compound_name_model();
		$this->_instrument_model = $this->get_instrument_model();
		$this->_ms_model = $this->get_ms_model();
		$this->_peak_model = $this->get_peak_model();
		$this->log = new Log4Massbank();
	}
	
	public function download_url_data($url, $headers, $download_file_path, $timeout = 60) 
	{
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
// 	        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
// 	        curl_setopt( $ch, CURLOPT_FILE, $fp );
// 			$output = curl_exec( $ch );
// 			curl_close( $ch );
// 			fclose( $fp );
		} else {
			$this->log->error("cURL is not installed");
		}
		return $output;
	}
	
	public function download_external_file($external_file_url, $internal_file_path)
	{
// 		file_put_contents($internal_file_path, fopen($external_file_url, 'r'));
	
		$newfname = $internal_file_path;
		$url = $external_file_url;
		
		$file = fopen ($url, "rb");
		if ( $file ) {
			$newf = fopen ($newfname, "a+");
// 			$newf = fopen ($newfname, "wb");
	
			if ( $newf ) {
				while( !feof($file) ) {
					fwrite( $newf, fread($file, 1024 * 8 ), 1024 * 8 );
				}
			}
		}
	
		if ( $file ) {
			fclose( $file );
		}
	
		if ( $newf ) {
			fclose( $newf );
		}
	}
	
	public function merge_msp_data($file_path)
	{
		$tbl_compound = array();
		$tbl_compound_names = array();
		$tbl_instrument = array();
		$tbl_ms = array();
		$tbl_peaks = array();
		
		$count = 0;
		
		$handle = fopen( $file_path, "r" );
		while ( !feof($handle) )
		{
			$line = fgets( $handle );
			
			//if ( empty(trim( $line )) && !empty($tbl_compound) ) {
// 			$this->log->info("[LINE] $line");
			if ( strlen( trim( $line ) ) == 0 && !empty($tbl_compound) ) {
// 				$this->log->info("[BREAK] break reading");
				$count++;
				
				// insert or update compound data into database
				$this->merge_into_database( $tbl_compound, $tbl_compound_names, $tbl_instrument, $tbl_ms, $tbl_peaks );
				
				// re-initialize
				$tbl_compound = array();
				$tbl_compound_names = array();
				$tbl_instrument = array();
				$tbl_ms = array();
				$tbl_peaks = array();
				
				$this->is_peak_lines = FALSE;
				$this->max_inte = 0;
			}
			// read the data: line by line
			$this->read_line( $line, $tbl_compound, $tbl_compound_names, $tbl_instrument, $tbl_ms, $tbl_peaks );
		}
		
		fclose( $handle );
		return $count;
	}
	
	public function remove_file($file_path)
	{
		unlink( $file_path );
	}
	
	private function read_line($line, &$tbl_compound, &$tbl_compound_names, &$tbl_instrument, &$tbl_ms, &$tbl_peaks)
	{
		// compound table
		if ( $this->startwith($line, Keyword::COMPOUND_ID) ) {
			$tbl_compound[Column::COMPOUND_ID] = $this->getvalue($line, Keyword::COMPOUND_ID);
		} else if ( $this->startwith($line, Keyword::COMPOUND_FORMULA) ) {
			$formula = $this->getvalue($line, Keyword::COMPOUND_FORMULA);
			$tbl_compound[Column::COMPOUND_FORMULA] = $formula;
			$tbl_compound[Column::COMPOUND_EXACT_MASS] = $this->calculate_mass($formula);
		} else if ( $this->startwith($line, Keyword::COMPOUND_AUTHORS) ) {
			$tbl_compound[Column::COMPOUND_AUTHORS] = $this->getvalue($line, Keyword::COMPOUND_AUTHORS);
		} else if ( $this->startwith($line, Keyword::COMPOUND_INSTRUMENT) ) {
			$tbl_compound[Column::COMPOUND_INSTRUMENT] = $this->getvalue($line, Keyword::COMPOUND_INSTRUMENT);
		} else if ( $this->startwith($line, Keyword::COMPOUND_LICENSE) ) {
			$tbl_compound[Column::COMPOUND_LICENSE] = $this->getvalue($line, Keyword::COMPOUND_LICENSE);
		} else if ( $this->startwith($line, Keyword::COMPOUND_ION_MODE) ) {
			$ion_mode = 0;
			$str_ion_mode = $this->getvalue($line, Keyword::COMPOUND_ION_MODE);
			if ( strcasecmp ( $str_ion_mode, 'POSITIVE' ) ) {
				$ion_mode = 1;
			} else if ( strcasecmp ( $str_ion_mode, 'NEGATIVE' ) ) {
				$ion_mode = -1;
			}
			$tbl_compound[Column::COMPOUND_ION_MODE] = $ion_mode;
		// compound name table
		} else if ($this->startwith($line, Keyword::COMPOUND_NAME_NAME)) {
			$str_name = $this->getvalue($line, Keyword::COMPOUND_NAME_NAME);
			$tbl_compound[Column::COMPOUND_TITLE] = $str_name;
	
			$name_parts = explode( ";", $str_name );
			$name_parts_length = sizeof( $name_parts );
			if ($name_parts_length > 0) {
				// name
				$tbl_compound_names[Column::COMPOUND_NAME_NAME][] = trim($name_parts[0]);
			}
			if ($name_parts_length > 2) {
				// ms
				preg_match('/MS[0-9]+|MS/', $str_name, $matches);
				$tbl_ms[Column::MS_TYPE_NAME] = trim($matches[0]);
			}
		// instrument
		} else if ($this->startwith($line, Keyword::INSTRUMENT_TYPE)) {
			$tbl_instrument[Column::INSTRUMENT_TYPE] = $this->getvalue($line, Keyword::INSTRUMENT_TYPE);
		// PubChem Id
		} else if ($this->startwith($line, Keyword::LINKS)) {
			foreach (explode(";", $this->getvalue($line, Keyword::LINKS)) as $link)
			{
				$link = trim($link);
				if ($this->startwith($link, "INCHIKEY")) {
					$tbl_compound[Column::PUBCHEM_ID] = $this->getvalue($link, "INCHIKEY");
					$tbl_compound[Column::PUBCHEM_ID_TYPE] = "inchikey";
				} elseif ($this->startwith($link, "PUBCHEM")) {
					$pubchem_id = $this->getvalue($link, "PUBCHEM");
					if ( !(strcasecmp($pubchem_id, "CID") == 0 || strcasecmp($pubchem_id, "SID") == 0) ) {
						$tbl5_compound[Column::PUBCHEM_ID] = $pubchem_id;
						$tbl_compound[Column::PUBCHEM_ID_TYPE] = "cid";
					}
				}
			}
		// peak
		} else if ( $this->is_peak_lines ) {
			$peak_lines_str = trim($line);
			$peak_lines = explode(";", $peak_lines_str);
			
			foreach ($peak_lines as $peak_line)
			{
				$peak_line = trim( $peak_line );
				if ( !empty ($peak_line) )
				{
					$peak_reads = explode("\t", $peak_line);
					
					if ( sizeof($peak_reads) < 2 ) {
						$peak_reads = explode(" ", $peak_line);
					}
					
					if ( sizeof($peak_reads) > 1 ) {
						$pMz = trim( $peak_reads[0] );
						$pRelInt = intval( trim( $peak_reads[1] ) );
						if ($this->max_inte < $pRelInt) {
							$this->max_inte = $pRelInt;
						}
						$tbl_peaks[] = array(
								Column::PEAK_MZ => $pMz,
								Column::PEAK_INTENSITY => $pRelInt
						);
					} else {
						
						$this->log->error( "Invalid peak line: " . $peak_line . " : peak should contain two values seperated by tab.");
					}
				}
				
			}
		}
		
		if ( !$this->is_peak_lines && $this->startwith($line, Keyword::NUM_PEAKS) ) {
			$this->is_peak_lines = TRUE;
		}
	}
	
	private function merge_into_database(&$tbl_compound, &$tbl_compound_names, &$tbl_instrument, &$tbl_ms, &$tbl_peaks)
	{
		
		if ( empty( $tbl_ms[Column::MS_TYPE_NAME] ) ) {
			$this->log->error( "empty ms type for " . $tbl_compound[Column::COMPOUND_ID] );
		}
		
		if ( empty( $tbl_instrument['INSTRUMENT_TYPE'] ) ) {
			$this->log->error( "empty instrument for " . $tbl_compound[Column::COMPOUND_ID] );
		}
		
// 		if ( empty( $tbl_ms[Column::MS_TYPE_NAME] ) || empty( $tbl_instrument['INSTRUMENT_TYPE'] ) ) {
			
// 			$this->log->error( "empty ms type or instrument for " . $tbl_compound[Column::COMPOUND_ID] );
			
// 		} else {
			
			foreach ($tbl_peaks as $key => $tbl_peak) {
				$tbl_peaks[$key][Column::PEAK_RELATIVE_INTENSITY] = intval ( ($tbl_peak[Column::PEAK_INTENSITY] / $this->max_inte) * 999 );
			}
			
			$instrument = NULL;
			$ms = NULL;
			
			// insert data into database
			
			$instrument_type = $tbl_instrument['INSTRUMENT_TYPE'];
			if ( !empty($instrument_type) )
			{
				$instrument = $this->_instrument_model->get_instrument_by_type($instrument_type);
				if ( !$instrument ) {
					$this->_instrument_model->insert($instrument_type);
					$instrument = $this->_instrument_model->get_instrument_by_type($instrument_type);
				}
			}
			
			if ( array_key_exists('MS_TYPE_NAME', $tbl_ms) ) 
			{
				$ms_type = $tbl_ms['MS_TYPE_NAME'];
				if ( !empty($ms_type) )
				{
					$ms = $this->_ms_model->get_ms_by_type($ms_type);
					if ( !$ms ) {
						$this->_ms_model->insert($ms_type);
						$ms = $this->_ms_model->get_ms_by_type($ms_type);
					}
				}
			}
			
// 			if ( isset($tbl_compound) && isset($ms) && isset($instrument) )
			if ( isset($instrument) )
			{
				
				$is_valid = TRUE;
				if ( ! array_key_exists(Column::COMPOUND_FORMULA, $tbl_compound) ) {
					$this->log->error("no formula information. " . var_export($tbl_compound, true));
					$is_valid = FALSE;
				}
				
				if ( $is_valid ) 
				{
					$this->_compound_model->merge(
							$tbl_compound[Column::COMPOUND_ID],
							$tbl_compound[Column::COMPOUND_TITLE],
							$tbl_compound[Column::COMPOUND_AUTHORS],
							$tbl_compound[Column::COMPOUND_INSTRUMENT],
							$tbl_compound[Column::COMPOUND_LICENSE],
							$tbl_compound[Column::COMPOUND_FORMULA],
							$tbl_compound[Column::COMPOUND_EXACT_MASS],
							$tbl_compound[Column::COMPOUND_ION_MODE],
							array_key_exists(Column::PUBCHEM_ID, $tbl_compound)? $tbl_compound[Column::PUBCHEM_ID]: NULL,
							array_key_exists(Column::PUBCHEM_ID_TYPE, $tbl_compound)? $tbl_compound[Column::PUBCHEM_ID_TYPE]: NULL,
							!empty($instrument)? $instrument[Column::INSTRUMENT_ID]: NULL,
							!empty($ms) ? $ms[Column::MS_TYPE_ID]: NULL,
							date('Y-m-d H:i:s'),
							date('Y-m-d H:i:s')
					);
				
					foreach ($tbl_compound_names[Column::COMPOUND_NAME_NAME] as $compound_name) {
						$this->_compound_name_model->merge(
								$tbl_compound[Column::COMPOUND_ID],
								$compound_name
						);
					}
			
					$this->_peak_model->delete_peaks_by_compound_id($tbl_compound[Column::COMPOUND_ID]);
					$this->_peak_model->bulk_insert($tbl_peaks, $tbl_compound[Column::COMPOUND_ID]);
				}
	
			}
			
// 		}
		
	}
	
	private function startwith($line, $pattern) {
		return (0 === strpos($line, $pattern));
	}
	
	private function getvalue($line, $pattern) {
		return trim(str_replace($pattern, '', $line));
	}
	
	private function calculate_mass($formula)
	{
		$mass_list = array(
				"H"  => 1.007825032,  "Be" => 9.0121821,    "B"  => 11.0093055,  "C"  => 12.00000000,
				"N"  => 14.003074005, "O"  => 15.994914622, "F"  => 18.99840320, "Na" => 22.98976967,
				"Al" => 26.98153844,  "Si" => 27.976926533, "P"  => 30.97376151, "S"  => 31.97207069,
				"Cl" => 34.96885271,  "V"  => 50.9439637,   "Cr" =>  51.9405119, "Fe" => 55.9349421,
				"Ni" => 57.9353479,   "Co" => 58.9332001,   "Cu" => 62.9296011,  "Zn" => 63.9291466,
				"Ge" => 73.9211782,   "Br" => 78.9183376,   "Mo" => 97.9054078,  "Pd" => 105.903483,
				"I"  => 126.904468,   "Sn" => 119.9021966,  "Pt" => 194.964774,  "Hg" => 201.970626
		);
	
		$atom_list = $this->get_atom_list($formula);
	
		$mass = 0;
		foreach ($atom_list as $atom => $num) {
			$mass += $mass_list[$atom] * $num;
		}
		// 		$mass = intval(($mass * 100000) + 0.5) / 100000;
		$mass = intval($mass * 100000) / 100000;
		return $mass;
	}
	
	private function get_atom_list($formula)
	{
		$atom_list = array();
	
		$start_pos = 0;
		$end_pos = strlen($formula);
	
		for ( $pos = $start_pos; $pos <= $end_pos; $pos++ )
		{
			$chr = "";
			if ( $pos < $end_pos ) {
				$chr = substr( $formula, $pos, 1 );
			}
	
			if ( $pos == $end_pos || ($pos > 0 && preg_match("/[\D]/", $chr) && strcmp($chr, strtoupper($chr)) == 0 ) )
			{
				$item = substr( $formula, $start_pos, $pos - $start_pos );
				$isFound = false;
				$pos1 = strlen($item);
	
				for ( $i = 1; $i < strlen($item); $i++ ) {
					$chr = substr( $item, $i, 1 );
					if ( preg_match("/[\d]/", $chr) ) {
						$pos1 = $i;
						$isFound = true;
						break;
					}
				}
	
				$atom = substr($item, 0, $pos1);
				$num = 1;
				if ( $isFound ) {
					$num = substr($item, $pos1);
				}
	
				if ( isset($atom_list[$atom]) ) {
					$num = $num + $atom_list[$atom];
				}
	
				$atom_list[$atom] = $num;
				$start_pos = $pos;
			}
		}
	
		return $atom_list;
	}
	
	private function get_headers_x($url,$format=0, $user='', $pass='', $referer='') {
		if (!empty($user)) {
			$authentification = base64_encode($user.':'.$pass);
			$authline = "Authorization: Basic $authentification\r\n";
		}
	
		if (!empty($referer)) {
			$refererline = "Referer: $referer\r\n";
		}
	
		$url_info=parse_url($url);
		$port = isset($url_info['port']) ? $url_info['port'] : 80;
		$fp=fsockopen($url_info['host'], $port, $errno, $errstr, 30);
		if($fp) {
			$head = "GET ".@$url_info['path']."?".@$url_info['query']." HTTP/1.0\r\n";
			if (!empty($url_info['port'])) {
				$head .= "Host: ".@$url_info['host'].":".$url_info['port']."\r\n";
			} else {
				$head .= "Host: ".@$url_info['host']."\r\n";
			}
			$head .= "Connection: Close\r\n";
			$head .= "Accept: */*\r\n";
			$head .= $refererline;
			$head .= $authline;
			$head .= "\r\n";
	
			fputs($fp, $head);
			while(!feof($fp) or ($eoheader==true)) {
				if($header=fgets($fp, 1024)) {
					if ($header == "\r\n") {
						$eoheader = true;
						break;
					} else {
						$header = trim($header);
					}
	
					if($format == 1) {
						$key = array_shift(explode(':',$header));
						if($key == $header) {
							$headers[] = $header;
						} else {
							$headers[$key]=substr($header,strlen($key)+2);
						}
						unset($key);
					} else {
						$headers[] = $header;
					}
				}
			}
			return $headers;
	
		} else {
			return false;
		}
	}
	
}
?>