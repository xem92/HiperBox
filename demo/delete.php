<?php
include_once("../libs/db.php");
include_once("../libs/filter.php");

$filter = new Filter();
$query = $filter->filterGet("query");

header("Content-Type:application/json");

switch ($query) {
	case 'deleteMusic':
		$music_id = $filter->filterGet("music-id");

		if(empty($music_id)){
			$response = array( "error" => "Invalid parameter. Waiting music-id to delete." );
			echo json_encode($response);
			exit;
		}

		$sql = "DELETE FROM music WHERE music_id=$music_id";
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