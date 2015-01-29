<?php
class Database
{
	
	public function __construct() {
		$this->open_connection();
	}
	
	public function listResult($sql, $parameters = NULL) {
		$query = $this->execute($sql, $parameters);
		return $query->fetchAll(PDO::FETCH_ASSOC);
	}
	
	public function uniqueResult($sql, $parameters = NULL) {
		$query = $this->execute($sql, $parameters);
		return $query->fetch(PDO::FETCH_ASSOC);
	}

	public function execute($sql, $parameters = NULL) {
		$query = $this->db->prepare($sql);
		if (empty($parameters)) {
			$query->execute();
		} else {
			$query->execute($parameters);
		}
		return $query;
	}
	
	/**
	 * Open the database connection with the credentials from application/config/config.php
	 */
	private function open_connection() {
		// set the (optional) options of the PDO connection. in this case, we set the fetch mode to
		// "objects", which means all results will be objects, like this: $result->user_name !
		// For example, fetch mode FETCH_ASSOC would return results like this: $result["user_name] !
		// @see http://www.php.net/manual/en/pdostatement.fetch.php
		$options = array (
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
			PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING
		);
	
		// generate a database connection, using the PDO connector
		// @see http://net.tutsplus.com/tutorials/php/why-you-should-be-using-phps-pdo-for-database-access/
		$this->db = new PDO ( DB_TYPE . ':host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET, DB_USER, DB_PASS, $options );
	}
	
}
?>