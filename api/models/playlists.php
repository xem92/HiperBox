<?php

/**
* 
*/
class Playlists
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

	public function getPlaylist($db, $playlist_id){
		$sql = "SELECT * FROM playlists WHERE playlist_id=$playlist_id";
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

	public function getPlayslists($db){
		$sql = <<<QUERY
SELECT
	pl.name,
	pl.playlist_id,
	m.title,
	m.music_id
FROM
	playlists pl
	INNER JOIN music m USING(playlist_id)
QUERY;

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

	public function insertPlaylist($db, $data){
		if(empty($data["name"])){
			throw new InvalidArgumentException("The column 'name' of playlist is mandatory.");
		}

		$sql = "INSERT INTO playlists (name) VALUES ('{$data["name"]}')";
		$db->query($sql)->execute();
		$response = array(
			"status"		=> "ok",
			"debug"			=> array(
 				"query"			=> $sql,
				"affected_rows" => $db->getAffectedRows(),
				"last_id"		=> $db->lastInsertID(),
			),
		);

		return $response;
	}

	public function updatePlaylist($db, $data){
		if(empty($data["name"]) && empty($data["playlist_id"])){
			throw new InvalidArgumentException("The column 'name' of playlist is mandatory.");
		}

		$sql = "UPDATE playlists SET name='{$data["name"]}' WHERE playlist_id={$data["playlist_id"]}";
		$db->query($sql)->execute();
		
		$response = array(
			"status"		=> "ok",
			"debug"			=> array(
 				"query"			=> $sql,
				"affected_rows" => $db->getAffectedRows(),
			),
		);

		return $response;
	}

	public function deletePlaylist($db, $data){

		if(empty($data["playlist_id"])){
			throw new InvalidArgumentException("The column 'playlist_id' of the song is mandatory.");
		}

		$sql = "DELETE FROM playlists WHERE playlist_id={$data["playlist_id"]} LIMIT 1";
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