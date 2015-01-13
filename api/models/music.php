<?php

class Music
{
	private static $instance;

	public static function getInstance()
	{
	  if ( !self::$instance instanceof self)
	  {
	     self::$instance = new self;
	  }
	  return self::$instance;
	}

	public function getMusic($db, $music_id){
		$sql = "SELECT * FROM music WHERE music_id=$music_id";
		$response = $db
			->query($sql)
			->execute()
			->getData();

		return array(
			"status"		=> "ok",
			"data"			=> $response,
			"debug"			=> array(
				"query"			=> $sql,
			),
		);
	}

	public function getMusicList($db){
		$sql = "SELECT * FROM music ORDER BY music_id DESC LIMIT 10";
		$response = $db
			->query($sql)
			->execute()
			->getData();

		return array(
			"status"		=> "ok",
			"data"			=> $response,
			"debug"			=> array(
				"query"			=> $sql,
			),
		);
	}

	public function getTrack($db, $track_id){
		$sql = "SELECT * FROM music WHERE track_id='$track_id'";
		$response = $db
			->query($sql)
			->execute()
			->getData();

		return array(
			"status"		=> "ok",
			"data"			=> $response,
			"debug"			=> array(
				"query"			=> $sql,
			),
		);
	}

	/**
	 * 
	 */
	public function insertMusic($db, $data){

		if(empty($data["artist"])){
			throw new InvalidArgumentException("The column 'artist' of the song is mandatory.");
		}
		if(empty($data["title"])){
			throw new InvalidArgumentException("The column 'title' of the song is mandatory.");
		}
		if(empty($data["playlist_id"])){
			throw new InvalidArgumentException("The column 'playlist_id' is mandatory.");
		}
		if(empty($data["track_id"])){
			throw new InvalidArgumentException("The column 'track_id' of the song is mandatory.");
		}

		$name = $data["artist"];

		$sql = "INSERT INTO music (artist,title,playlist_id,track_id) VALUES ('{$data["artist"]}','{$data["title"]}',{$data["playlist_id"]},'{$data["track_id"]}')";
		$db->query($sql)->execute();

		return array(
			"status"		=> "ok",
			"debug"			=> array(
				"query"			=> $sql,
				"affected_rows" => $db->getAffectedRows(),
				"last_id"		=> $db->lastInsertID(),
			),
		);
	}

	/**
	 * 
	 */
	public function updateMusic($db, $data){

		if(empty($data["music_id"])){
			throw new InvalidArgumentException("The column 'music_id' of the song is mandatory.");
		}

		$music_id = $data["music_id"];
		unset( $data["music_id"] );

		if( !array_intersect(array_keys($data), array('artist','title','playlist_id','track_id')) ){
			throw new InvalidArgumentException("The columns (". implode(",", array_keys($data)) .") are not correct.");
		}

		$set = array();
		foreach( $data as $column => $value ){
			$set[] = "$column='$value'";
		}

		$set = implode(",", $set);	

		$sql = <<<QUERY
UPDATE music
SET $set
WHERE music_id=$music_id
QUERY;
		$db->query($sql)->execute();

		return array(
			"status"		=> "ok",
			"debug"			=> array(
				"query"			=> $sql,
				"affected_rows" => $db->getAffectedRows(),
			),
		);
	}

	public function deleteMusic($db, $data){

		if(empty($data["music_id"])){
			throw new InvalidArgumentException("The column 'music_id' of the song is mandatory.");
		}

		$sql = "DELETE FROM music WHERE music_id={$data["music_id"]} LIMIT 1";
		$db->query($sql)->execute();

		return array(
			"status"		=> "ok",
			"debug"			=> array(
				"query"			=> $sql,
				"affected_rows" => $db->getAffectedRows(),
			),
		);
	}
}