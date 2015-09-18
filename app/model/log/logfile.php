<?php
class LogFile {
	
	private $LOGFILENAME;
	private $LOGFILEDIR;
	private $SEPARATOR = "\t";
	private $LOGFILE_RESOURCE;
	
	private $starttime;
	
	public function __construct($file_prefix = "webhook", $dirpath = LOG_FILE_FOLDER)
	{
		$this->LOGFILENAME = $file_prefix;
		$this->LOGFILEDIR = rtrim($dirpath, '/') . '/';
		$this->LOGFILE_RESOURCE = $this->LOGFILEDIR . $this->LOGFILENAME . "-resource.log";
	}
	
	public function remove() {
		unlink($this->LOGFILEDIR . $this->LOGFILENAME . "-resource.log");
	}
	
	public function get_filename() {
		return $this->LOGFILENAME . "-resource.log";
	}
	
	public function push_resource($message)
	{
		$this->log( 'PUSH', $this->LOGFILE_RESOURCE, $message );
	}
	
	public function pop_resource()
	{
		$this->log( 'POP', $this->LOGFILE_RESOURCE );
	}
	
	private function log($log_level, $file_name, $message = NULL)
	{
		if ( !empty($log_level) ) {
			
			
			if ( strcmp($log_level, 'PUSH') == 0 ) {
					
				$fd = fopen( $file_name, "a" ) or die( "Can't create file" );
				$entry = array( $message );
				fwrite( $fd, print_r( "\n" . implode( $this->SEPARATOR, $entry ), TRUE ) );
				fclose( $fd );
				
			} elseif ( strcmp($log_level, 'POP') == 0 ) {
				
				$contents = file( $file_name, FILE_IGNORE_NEW_LINES );
				$first_line = array_shift( $contents );
				file_put_contents( $file_name, implode("\n", $contents) );
				return $first_line;
				
			}
			
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