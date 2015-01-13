<?php
include_once("../libs/db.php");
include_once("../libs/filter.php");

$filter = new Filter();
$query = $filter->filterGet("query");

header("Content-Type:application/json");


switch ($query) {
	case 'insertMusic':
		$artist = $filter->filterGet("artist");
		$track_id = $filter->filterGet("track-id");

		if(empty($artist) || empty($track_id)){
			$response = array( "error" => "Invalid parameters. Waiting artist and track_id to insert." );
			echo json_encode($response);
			exit;
		}

		$sql = "INSERT INTO music (track_id,artist) VALUES('$track_id', '$artist')";

		break;
	default:
		exit;
}

$db_configuration = array(
	"username"	=> "hiper_db_user",
	"password"	=> "123456",
	"database"	=> "hipermedia",
);

$db = new Db($db_configuration);
$db->connect();

$db->query($sql)->execute();
$response = array(
	"query"			=> $sql,
	"affected_rows" => $db->getAffectedRows(),
);

$db->closeConnection();



echo json_encode($response);
?>