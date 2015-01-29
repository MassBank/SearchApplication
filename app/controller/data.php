<?php

require_once APP. '/entity/keyword.php';
require_once APP. '/entity/column.php';

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
		$this->_instrument_model = $this->loadModel('instrument_model');
		$this->_ms_model = $this->loadModel('ms_model');
		$this->_compound_model = $this->loadModel('compound_model');
		$this->_compound_name_model = $this->loadModel('compound_name_model');
		$this->_peak_model = $this->loadModel('peak_model');
		
// 		$this->_instrument_model->drop_table();
// 		$this->_ms_model->drop_table();
// 		$this->_compound_model->drop_table();
// 		$this->_compound_name_model->drop_table();
// 		$this->_peak_model->drop_table();

		$this->_peak_model->delete_all();
		$this->_compound_name_model->delete_all();
		$this->_compound_model->delete_all();
		$this->_instrument_model->delete_all();
		$this->_ms_model->delete_all();

		$this->_ms_model->create_table_if_not_exists();
		$this->_instrument_model->create_table_if_not_exists();
		$this->_compound_model->create_table_if_not_exists();
		$this->_compound_name_model->create_table_if_not_exists();
		$this->_peak_model->create_table_if_not_exists();
		
		$dir_path = 'C:/Apps/Documents/Projects/proj-massbank/massbankrecord';

		$files = $this->listdir($dir_path);
		sort($files, SORT_LOCALE_STRING);
		
		foreach ($files as $f) {
		
			$tbl_compound = array();
			$tbl_compound_names = array();
			$tbl_instrument = array();
			$tbl_ms = array();
			$tbl_peaks = array();
		
			$file = fopen($f, "r");
			$prev_line = NULL;
			while(!feof($file)){
				$line = fgets($file);
				if ($this->startwith($line, Keyword::EOF)) {
					break;
				}
				// compound table
				if ($this->startwith($line, Keyword::COMPOUND_ID)) {
					$tbl_compound[Column::COMPOUND_ID] = $this->getvalue($line, Keyword::COMPOUND_ID);
				} else if ($this->startwith($line, Keyword::COMPOUND_TITLE)) {
					$tbl_compound[Column::COMPOUND_TITLE] = $this->getvalue($line, Keyword::COMPOUND_TITLE);
				} else if ($this->startwith($line, Keyword::COMPOUND_FORMULA)) {
					$tbl_compound[Column::COMPOUND_FORMULA] = $this->getvalue($line, Keyword::COMPOUND_FORMULA);
				} else if ($this->startwith($line, Keyword::COMPOUND_EXACT_MASS)) {
					$tbl_compound[Column::COMPOUND_EXACT_MASS] = $this->getvalue($line, Keyword::COMPOUND_EXACT_MASS);
				} else if ($this->startwith($line, Keyword::COMPOUND_ION_MODE)) {
					$ion_mode = 0;
					$str_ion_mode = $this->getvalue($line, Keyword::COMPOUND_ION_MODE);
					if ($str_ion_mode == 'POSITIVE') {
						$ion_mode = 1;
					} else if ($str_ion_mode = 'NEGATIVE') {
						$ion_mode = -1;
					}
					$tbl_compound[Column::COMPOUND_ION_MODE] = $ion_mode;
				// compound name table
				} else if ($this->startwith($line, Keyword::COMPOUND_NAME_NAME)) {
					$tbl_compound_names[Column::COMPOUND_NAME_NAME][] = $this->getvalue($line, Keyword::COMPOUND_NAME_NAME);
				// instrument
				} else if ($this->startwith($line, Keyword::INSTRUMENT_TYPE)) {
					$tbl_instrument[Column::INSTRUMENT_TYPE] = $this->getvalue($line, Keyword::INSTRUMENT_TYPE);
				// ms
				} else if ($this->startwith($line, Keyword::MS_TYPE)) {
					$tbl_ms[Column::MS_TYPE] = $this->getvalue($line, Keyword::MS_TYPE);
				// peak
				} else if (!empty($prev_line) && $this->startwith($prev_line, Keyword::PEAK)) {
					$peak_line = trim($line);
					$peak_reads = explode(" ", $peak_line);
					$tbl_peaks[] = array(
						Column::PEAK_MZ => $peak_reads[0],
						Column::PEAK_INTENSITY => $peak_reads[1],
						Column::PEAK_RELATIVE_INTENSITY => $peak_reads[2]
					);
				}
				if (!$this->startwith($line, Keyword::BLANK)) {
					$prev_line = $line;
				}
			}
			fclose($file);
			
			// insert data into database
			
			$instrument_type = $tbl_instrument['INSTRUMENT_TYPE'];
			$instrument = $this->_instrument_model->get_instrument_by_type($instrument_type);
			if (!$instrument) {
				$this->_instrument_model->insert($instrument_type);
				$instrument = $this->_instrument_model->get_instrument_by_type($instrument_type);
			}
			
			$ms_type = $tbl_ms['MS_TYPE'];
			$ms = $this->_ms_model->get_ms_by_type($ms_type);
			if (!$ms) {
				$this->_ms_model->insert($ms_type);
				$ms = $this->_ms_model->get_ms_by_type($ms_type);
			}
			
			$this->_compound_model->insert(
					$tbl_compound[Column::COMPOUND_ID],
					$tbl_compound[Column::COMPOUND_TITLE],
					$tbl_compound[Column::COMPOUND_FORMULA],
					$tbl_compound[Column::COMPOUND_EXACT_MASS],
					$tbl_compound[Column::COMPOUND_ION_MODE],
					$ms[Column::MS_ID],
					$instrument[Column::INSTRUMENT_ID]
					);
			
			foreach ($tbl_compound_names[Column::COMPOUND_NAME_NAME] as $tbl_compound_name) {
				$this->_compound_name_model->insert(
						$tbl_compound_name,
						$tbl_compound[Column::COMPOUND_ID]
						);
			}

			foreach ($tbl_peaks as $tbl_peak) {
				$this->_peak_model->insert(
						$tbl_peak[Column::PEAK_MZ],
						$tbl_peak[Column::PEAK_INTENSITY],
						$tbl_peak[Column::PEAK_RELATIVE_INTENSITY],
						$tbl_compound[Column::COMPOUND_ID]
						);
			}
			
		}
		
	}

	
	// file
	
	private function startwith($line, $pattern) {
		return (0 === strpos($line, $pattern));
	}
	
	private function getvalue($line, $pattern) {
		return trim(str_replace($pattern, '', $line));
	}
	
	private function listdir($dir='.') {
		if (!is_dir($dir)) {
			return false;
		}
	
		$files = array();
		$this->listdiraux($dir, $files);
	
		return $files;
	}
	
	private function listdiraux($dir, &$files) {
		$handle = opendir($dir);
		while (($file = readdir($handle)) !== false) {
			if (sizeof($files) < $this->sync_file_count) {
				if ($file == '.' || $file == '..' ||
						$file == ".svn" || $file == "massbankdb" || $file == "massbankindex") {
					continue;
				}
				$filepath = $dir == '.' ? $file : $dir . '/' . $file;
				if (is_link($filepath)) {
					continue;
				}
				if (is_file($filepath)) {
						$files[] = $filepath;
				} else if (is_dir($filepath)) {
					$this->listdiraux($filepath, $files);
				}
			} else {
				break;
			}
		}
		closedir($handle);
	}

}

?>