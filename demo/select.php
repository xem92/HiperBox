<?php
include_once("../libs/db.php");
include_once("../libs/filter.php");

$filter = new Filter();
$query = $filter->filterGet("query");

switch ($query) {
	case 'getMusic':
		$sql = "SELECT * FROM music";
		break;
	case 'getPlaylists':
		$sql = "SELECT * FROM playlist";
		break;
	default:
		$sql = "SELECT * FROM music LIMIT 10";
}


$db_configuration = array(
	"username"	=> "hiper_db_user",
	"password"	=> "123456",
	"database"	=> "hipermedia",
);

$db = new Db($db_configuration);
$db->connect();

$response = $db
	->query($sql)
	->execute()
	->getData();

$db->closeConnection();


header("Content-Type:application/json");
echo json_encode($response);