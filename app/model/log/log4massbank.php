<?php
class Log4Massbank {
	
	private $LOGFILENAME;
	private $LOGFILEDIR;
	private $SEPARATOR = "\t";
	private $FILE_EXT = "";
// 	private $SEPARATOR = ",";
// 	private $FILE_EXT = ".csv";
	private $LOGFILE_INFO;
	private $LOGFILE_ERROR;
	private $LOGFILE_WARNING;
	private $LOGFILE_DEBUGING;
	
	private $starttime;
	
	public function __construct($filename = LOG_FILE_PREFIX, $dirpath = LOG_FILE_FOLDER)
	{
		$this->LOGFILENAME = $filename;
		$this->LOGFILEDIR = rtrim($dirpath, '/') . '/';
		$this->LOGFILE_INFO = $this->LOGFILEDIR . $this->LOGFILENAME . "-" . date('Ymd'). "-info.log" . $this->FILE_EXT;
		$this->LOGFILE_ERROR = $this->LOGFILEDIR . $this->LOGFILENAME . "-" . date('Ymd'). "-error.log" . $this->FILE_EXT;
		$this->LOGFILE_WARNING = $this->LOGFILEDIR . $this->LOGFILENAME . "-" . date('Ymd'). "-warning.log" . $this->FILE_EXT;
		$this->LOGFILE_DEBUGING = $this->LOGFILEDIR . $this->LOGFILENAME . "-" . date('Ymd'). "-debug.log" . $this->FILE_EXT;
	}
	
	public function start()
	{
		$mtime = microtime();
		$mtime = explode( " ", $mtime );
		$mtime = $mtime[1] + $mtime[0];
		$this->starttime = $mtime;
	}
	
	public function end()
	{
		$mtime = microtime();
		$mtime = explode( " ", $mtime );
		$mtime = $mtime[1] + $mtime[0];
		$endtime = $mtime;
		$totaltime = ( $endtime - $this->starttime );
		$this->log( 'INFO', $this->get_calling_class() . ":" . $this->get_calling_function() . " " . round( $totaltime, 4 ) . " (s)", $this->LOGFILE_INFO );
	}
	
	public function info($message)
	{
		$this->log( 'INFO', $message, $this->LOGFILE_INFO );
	}
	
	public function error($message)
	{
		$this->log( 'ERROR', $message, $this->LOGFILE_ERROR );
	}
	
	public function warning($message)
	{
		$this->log( 'WARN', $message, $this->LOGFILE_WARNING );
	}
	
	public function debug($message)
	{
		$this->log( 'DEBUG', $message, $this->LOGFILE_DEBUGING );
	}
	
	private function log($log_level, $message, $file)
	{
		if ( ini_get('log_errors') == 1 && !empty($log_level) && !empty($message) ) {
			
// 			$fd = NULL;
// 			if (!file_exists($file)) {
// 				$fd = fopen($file, 'w') or die("Can't create file");
// 			} else {
// 				$fd = fopen($file, "a");
// 			}
			
			$fd = fopen($file, "a") or die("Can't create file");
			
			$datetime = @date("Y-m-d H:i:s");
			$debugBacktrace = debug_backtrace();
			$line = $debugBacktrace[1]['line'];
			$file = $debugBacktrace[1]['file'];
			
			$message = preg_replace( '/\s+/', ' ', trim($message) );
			$entry = array( "[" . $datetime . "]", "[" . $log_level . "]", "[" . $file . ":" . $line . "]", $message );
			
// 			fputcsv($fd, $entry, $this->SEPARATOR);
			fwrite( $fd, print_r( implode( $this->SEPARATOR, $entry ), TRUE ) . "\n" );
			
			fclose( $fd );
		}
	}
	
	private function get_calling_class() {
	
	    //get the trace
	    $trace = debug_backtrace();
	
	    // Get the class that is asking for who awoke it
	    $class = $trace[1]['class'];
	
	    // +1 to i cos we have to account for calling this function
	    for ( $i=1; $i<count( $trace ); $i++ ) {
	        if ( isset( $trace[$i] ) ) { // is it set?
	             if ( $class != $trace[$i]['class'] ) { // is it a different class
	                 return $trace[$i]['class'];
	             }
	        }
	    }
	    
	}
	
	private function get_calling_function() {
	
	    //get the trace
	    $trace = debug_backtrace();
	
	    // Get the class that is asking for who awoke it
	    $fn = $trace[1]['function'];
	
	    // +1 to i cos we have to account for calling this function
	    for ( $i=1; $i<count( $trace ); $i++ ) {
	        if ( isset( $trace[$i] ) ) { // is it set?
	             if ( $fn != $trace[$i]['function'] ) { // is it a different class
	                 return $trace[$i]['function'];
	             }
	        }
	    }
	    
	}
	
}
?>