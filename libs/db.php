<?php
class Db
{
	const HOST = "localhost:3360";

	var $databaseLink;
	var $database;
	var $username;
	var $password;

	var $sql;
	var $resource;
	var $affected_rows;

	public function __construct( array $db_configuration ){
		if(
			!isset($db_configuration["username"])
			|| !isset($db_configuration["password"])
			|| !isset($db_configuration["database"])
		){
			throw new InvalidArgumentException("Invalid database configuration! You must define an array with the username, password and database.");
		}

		$this->username = $db_configuration["username"];
		$this->password = $db_configuration["password"];
		$this->database = $db_configuration["database"];
	}

	public function connect(){
		$this->closeConnection();

		$this->databaseLink =  mysql_connect(self::HOST, $this->username, $this->password);
		if (!$this->databaseLink) {
			throw new RuntimeException("Error when connecting to database {$this->database}: ". mysql_error());
		}

		mysql_select_db($this->database, $this->databaseLink);

		return $this;
	}

	public function query($sql){
		$this->sql = $sql;
		return $this;
	}

	public function execute(){
		$this->resource = mysql_query($this->sql, $this->databaseLink);
		if(empty($this->resource)){
			throw new RuntimeException(mysql_error());
		}

		$this->affected_rows = mysql_affected_rows($this->databaseLink);

		return $this;
	}

	public function lastInsertID(){
        return mysql_insert_id($this->databaseLink);
    }

    public function getAffectedRows(){
        return $this->affected_rows;
    }

	public function getData(){
		$array_response = array();

		while ($data = mysql_fetch_assoc($this->resource)){
        	$array_response[] = $data;
        }

		return $array_response;
	}

	public function closeConnection(){
		if($this->databaseLink){
			$this->commit();
			mysql_close($this->databaseLink);
		}

		return $this;
	}

	private function commit(){
		return mysql_query("COMMIT", $this->databaseLink);
	}
  
	private function rollback(){
		return mysql_query("ROLLBACK", $this->databaseLink);
	}
}
?>