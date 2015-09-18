<?php

class MqQueue
{
	
	/**
	 * Stores our queue semaphore.
	 * @var resource
	 */
	private static $queue = NULL;
	
	/**
	 * getQueue: Returns the semaphore message resource.
	 *
	 * @access public
	 */
	public static function getQueue() {
	
		# Setup the queue
		self::$queue = msg_get_queue(QUEUE_KEY);
	
		# Return the queue
		return self::$queue;
	
	}
	
	/**
	 * addMessage: Given a key, store a new message into our queue.
	 *
	 * @param $key string - Reference to the message (PK)
	 * @param $data array - Some data to pass into the message
	 */
	public static function addMessage($key, $data = array()) {
		$log = new Log4Massbank();
		if ( !isset(self::$queue) ) {
			self::$queue = MqQueue::getQueue();
		}
		# What to send
		$log->info("[START] create Mq Message");
		$message = new MqMessage($key, $data);
		$log->info("[END] create Mq Message");
		# Try to send the message
		$log->info("[START] send Mq Message:" . var_export($message, true));
		if(msg_send(self::$queue, QUEUE_TYPE_START, $message)) {
			$log->info("Added to the queue");
// 			$log->info(var_export(msg_stat_queue(self::$queue), true));
		} else {
			$log->error("Error adding to the queue");
		}
		$log->info("[END] send Mq Message");
	}
	
}
?>