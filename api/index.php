<?php
require_once '../libs/api.php';
require_once '../libs/db.php';
class sqlAPI extends API
{
    private $db;
    private $db_configuration = array(
        "username"  => "hiper_db_user",
        "password"  => "123456",
        "database"  => "hipermedia",
    );

    public function __construct($request, $origin) {
        parent::__construct($request);

        $this->dbConnect();
    }

    public function __destruct(){
        $this->db->closeConnection();
    }

    /**
     * Execute any query.
     * curl -X PUT --data "sql={sql}" {domain}/query
     */
    public function query(){
        if( "PUT" === $this->method){
            if( empty( $this->file ) )
            {
                throw new InvalidArgumentException("Invalid sql!! The application need a 'sql' parameter defined.");  
            }

            try{ 
                $sql = $this->file;

                $response = $this->db
                    ->query($sql)
                    ->execute();

                $return = array(
                    "status"        => "ok",
                    "response"      => preg_match("/select/", strtolower($sql))? $response->getData() : "",
                    "debug"         => array(
                        "query"         => $sql,
                        "affected_rows" => $this->db->getAffectedRows(),
                        "last_id"       => $this->db->lastInsertID(),
                    ),
                );

                return $return;

            } catch( Exception $e ){
                return array(
                    "status"    => "error", 
                    "message"   => $e->getMessage(),
                    "debug"         => array(
                        "query"     => $sql,
                    ),
                );
            }
        }
        return array("status" => "error", "message" => "You must use a PUT method to execute a query, using the body to send the sql.");
    }

    /**
     * Get playlist: GET {domain}/playlist/{playlist_id}
     * curl -X GET {domain}/playlist/{playlist_id}
     * 
     * Get list of playlists: GET {domain}/playlist
     * curl -X GET {domain}/playlist
     *
     * Insert playlist: POST {domain}/playlist/new
     * curl -X POST --data "name={name}" {domain}/playlist/new
     *
     * Update playlist: POST {domain}/playlist/update
     * curl -X POST --data "name={name}&playlist_id={playlist_id}" {domain}/playlist/update
     *
     */
    public function playlists($args){
        require_once 'models/playlists.php';

        if( "GET" == $this->method )
        {
            if(!empty($args[0])){
                return Playlists::getInstance()->getPlaylist($this->db, $args[0]);
            }

            return Playlists::getInstance()->getPlayslists($this->db);
        }
        elseif ("POST" == $this->method && "new" == $this->verb ) {
            return Playlists::getInstance()->insertPlaylist($this->db, $this->request);
        }
        elseif ("POST" == $this->method && "update" == $this->verb ) {
            return Playlists::getInstance()->updatePlaylist($this->db, $this->request);
        } elseif ("DELETE" == $this->method ) {
            return Playlists::getInstance()->deletePlaylist($this->db, $this->request);
        }

        return array("status" => "error", "message" => "Check your GET&POST methods. The data don't match.");
    }

    /**
     * Get music: GET {domain}/music/{music_id}
     * curl -X GET {domain}/music/{music_id}
     * 
     * Get 10 last music: GET {domain}/music
     * curl -X GET {domain}/music
     *
     * Insert music: POST {domain}/music/new
     * curl -X POST --data "artist={artist}&title={title}&track_id={track_id}&playlist_id={playlist_id}" {domain}/music/new
     *
     * Update music: POST {domain}/music/update
     * curl -X POST --data "playlist_id={playlist_id}&music_id={music_id}" {domain}/music/update
     *
     */
    public function music($args){
        require_once 'models/music.php';

        if( "GET" == $this->method )
        {
            if(!empty($args[0])){
                return Music::getInstance()->getMusic($this->db, $args[0]);
            
            }else{
                return Music::getInstance()->getMusicList($this->db);
            } 
        } elseif ("POST" == $this->method && "new" == $this->verb  ) {
            return Music::getInstance()->insertMusic($this->db, $this->request);
        } elseif ("POST" == $this->method && "update" == $this->verb  ) {
            return Music::getInstance()->updateMusic($this->db, $this->request);
        } elseif ("DELETE" == $this->method ) {
            return Music::getInstance()->deleteMusic($this->db, $this->request);
        }

        return array("status" => "error", "message" => "Check your GET&POST methods. The data don't match.");
    }

    /**
     * Get track: GET {domain}/track/id/{track_id}
     * curl -X GET {domain}/track/id/{track_id}
     */
    public function track($args){
        require_once 'models/music.php';

        if( "GET" == $this->method )
        {
            if(!empty($args[0])){
                return Music::getInstance()->getTrack($this->db, $args[0]);
            }
        }

        return array("status" => "error", "message" => "Check your GET&POST methods. The data don't match.");
    }

    private function dbConnect(){
        $this->db = new Db($this->db_configuration);
        $this->db->connect();
    }
} 

class Exception_302 extends Exception
{
    public function __construct($url)
    {
        header("HTTP/1.1 302 Moved Temporarily"); 
        header("Location: $url");
    }
}

 // Requests from the same server don't have a HTTP_ORIGIN header
if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
    $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}

try {
    if(empty($_REQUEST['request']))
    {
        throw new Exception_302("http://api.hipermedia.local/doc/index.html");
    }
    $API = new sqlAPI($_REQUEST['request'], $_SERVER['HTTP_ORIGIN']);
    echo $API->processAPI();
} catch (Exception $e) {
    $response = array(
        "status"        => "error",
        "message"       => $e->getMessage(),
    );
    echo json_encode($response);
}
?>