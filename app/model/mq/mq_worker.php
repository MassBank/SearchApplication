<?php

require_once APP . '/model/util/string_builder.php';

require_once APP . '/model/mq/mq_message.php';
require_once APP . '/model/mq/mq_queue.php';

class MqWorker {
	
	private $log;
	/**
	 * Store the semaphore queue handler.
	 * @var resource
	 */
	private $queue = NULL;
	/**
	 * Store an instance of the read Message
	 * @var Message
	 */
	private $message = NULL;
	/**
	 * Constructor: Setup our enviroment, load the queue and then
	 * process the message.
	 */
	public function __construct() {
		$this->log = new Log4Massbank();
		# Get the queue
		$this->queue = MqQueue::getQueue();
		# Now process
		$this->process();
	}
	
	private function process() {
		$messageType = NULL;
		$messageMaxSize = 1024;
		# Loop over the queue
		$this->log->info("[START] mq receive process");
		
		$loop_status = true;
		while( $loop_status ) {
			msg_receive( $this->queue, QUEUE_TYPE_START, $messageType, $messageMaxSize, $this->message );
			$queue_status = msg_stat_queue($this->queue);
			$loop_status = ( $queue_status['msg_qnum'] != 0 );
			$this->log->info('[COUNT] messages in the queue: '.$queue_status['msg_qnum']);
			# We have the message, fire back
			$this->complete( $messageType, $this->message );
			# Reset the message state
			$messageType = NULL;
			$this->message = NULL;
			sleep(1);
		}
		
		$this->log->info("[END] mq receive process");
	}
	/**
	 * complete: Handle the message we read from the queue
	 *
	 * @param $messageType int - The type we actually got, not what we desired
	 * @param $message Message - The actual object
	 */
	private function complete($messageType, MqMessage $message) {
		# Generic method
		$this->log->info("[START] worker complete");
		$data = $message->getData();
		$sb = new String_Builder();
		$sb->append( $data['host-url'] . "data/merge" );
		$sb->append( "?resource=" . $data['resource'] );
		if ( isset($data['media_types']) ) {
			foreach ( $data['media_types'] as $media_type ) {
				$sb->append( "&media_types[]=" . $media_type );
			}
		}
		$data_url = $sb->to_string();
		$this->log->info("[URL] server URL: " . $data_url);
		$response = file_get_contents( $data_url );
		$this->log->info("[END] worker complete");
	}
}
?>