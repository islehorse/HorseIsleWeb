<?php
$host = $_SERVER['HTTP_HOST'];

$cfgDir = getenv("HISP_CONFIG_DIR");
$cfgFile = getenv("HISP_CONF_FILE");
$serverFile = getenv("HISP_SERVER_FILE");
$gameCfgFile = getenv("HISP_GAME_CFG_FILE");

if($cfgFile == null)
	$cfgFile = "web.cfg";

if($gameCfgFile == null)
	$gameCfgFile = "game1.cfg";

if($serverFile == null)
	$serverFile = "servers.json";

if($cfgDir == null) {
	$cfgDir = "/etc/hisp";
	if(php_uname('s') === "Windows NT") {
		$cfgDir = getenv("APPDATA")."\\"."HISP";
	}
}

define("CFG_DIR", $cfgDir);
define("CFG_FILE", $cfgDir . "/" . $cfgFile);
define("CFG_FILE_GAME", $cfgDir . "/" . $gameCfgFile);
define("SRV_FILE", $cfgDir . "/" . $serverFile);

function handle_cfg_line(array &$cfg, string $line) {
	$kvp = explode("=", $line);
	if(sizeof($kvp) != 2) return;
	$cfg[strtoupper($kvp[0])] = str_replace("\n", "", str_replace("\r", "", $kvp[1]));
}
function gen_servers(string $path) {
	if(!file_exists($path)) {
		$file_data = file_get_contents("web/base_servers.json");
		file_put_contents($path, $file_data);
	}
}

function gen_game_cfg(string $path) {
	if(!file_exists($path)) {
		$file_data = file_get_contents("web/base_game.cfg");
		file_put_contents($path, $file_data);		
	}
}

function gen_cfg(string $path) {
	if(!file_exists($path)) {
		$file_data = file_get_contents("web/base_web.cfg");
		file_put_contents($path, $file_data);		
	}
}

function get_servers() {
	gen_servers(SRV_FILE);
	$data = json_decode(file_get_contents(SRV_FILE), true);
	return $data;
}


function parse_cfg(string $path) {
	$fd = fopen($path, "rb");
	
	$cfg = array();
	
	while($line = fgets($fd)) {
		if(strlen($line) <= 0) continue;
		if($line == "") continue;
		if(startsWith($line,"#")) continue;
		
		handle_cfg_line($cfg, $line);
	}
	
	fclose($fd);
	return $cfg;
}

function get_cfg() {
	$path = CFG_FILE;
	gen_cfg($path);
	return parse_cfg($path);
}

function get_cfg_game() {
	$path = CFG_FILE_GAME;
	gen_game_cfg($path);
	$cfg = parse_cfg($path);
	$cfg = array_merge($cfg, parse_cfg($cfg["GAME_SERVER_PROPRETIES"]));
	return $cfg;
}

function hash_salt(string $input, string $salt)
{
	$output = hash('sha512',$input,true);
	$len=strlen(bin2hex($output))/2;
	$xor_hash = "";
	for($i = 0; $i < $len; $i++)
	{
		$xor_hash .= $output[$i] ^ $salt[$i];
	}
	
	return hash('sha512',$xor_hash,false);
}

function base64_url_encode($input) {
 return strtr(base64_encode($input), '+/=', '._-');
}

function base64_url_decode($input) {
 return base64_decode(strtr($input, '._-', '+/='));
}

function is_logged_in()
{
	if(session_status() !== PHP_SESSION_ACTIVE)
		return false;
	
	if(isset($_SESSION["LOGGED_IN"]))
		if($_SESSION["LOGGED_IN"] === "YES")
			return true;
	return false;
}


function sql_connect(?string $override_db = null) {
	$cfg = get_cfg_game();
	
	$db = $cfg["DB_NAME"];
	if($override_db != null)
		$db = $override_db;
	
	$connect = mysqli_connect($cfg["DB_IP"], $cfg["DB_USERNAME"], $cfg["DB_PASSWORD"],$db) or die("Unable to connect to database");
	return $connect;
}

function user_exists(string $username)
{
	
	$usernameUppercase = strtoupper($username);
	$connect = sql_connect();
	$stmt = $connect->prepare("SELECT COUNT(1) FROM Users WHERE UPPER(Username)=?"); 
	$stmt->bind_param("s", $usernameUppercase);
	$stmt->execute();
	$result = $stmt->get_result();
	$count = intval($result->fetch_row()[0]);
	return $count>0;
}

function get_username(string $id)
{
	
	$connect = sql_connect();
	$stmt = $connect->prepare("SELECT Username FROM Users WHERE Id=?"); 
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$result = $stmt->get_result();
	$usetname = $result->fetch_row()[0];
	return $usetname;
}

function get_protocol(){
	if(!isset($_SERVER['HTTPS'])){
		return "http://";
	}
	
	if($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1){
		return "https://";
	}
	else{
		return "http://";
	}
}


function api_send(string $serverId, string $req, $data) {
	$dataenc = base64_url_encode(json_encode($data));
	$hmac = GenHmacMessage($req . $dataenc, "HORSEISLE-CROSSERVER-REQUEST", false);
	
	$server = getServerById($serverId);
	
	return json_decode(file_get_contents($server["internal_site"] . "api.php?req=" . $req . "&data=" . $dataenc . "&hmac=" . $hmac));
}


function get_host(){
	return $_SERVER['HTTP_HOST'];
}

function GenHmacMessage(string $data, string $channel, bool $restricted=true)
{
	$cfg = get_cfg();
	
	if($cfg["HMAC_SECRET"] === "!!NOTSET!!") {
		echo("<script>alert('Please set HMAC_SECRET !')</script>");
		echo("<h1>Set HMAC_SECRET in web.cfg!</h1>");
		exit();
	}
	
	$secret = $cfg["HMAC_SECRET"].$channel;
	
	if($restricted)
		$secret .= $_SERVER['REMOTE_ADDR'].date('mdy');
	
	$hmac = hash_hmac('sha256', $data, $secret);
	return $hmac;
}

function send_activation_email(string $email, string $username, string $password){

	$cfg = get_cfg();
	
	$hmac = GenHmacMessage($username, "UserActivation", false);
	$hmacSignature = base64_url_encode(hex2bin($hmac));
	$activateUrl = get_protocol().get_host()."/web/newuser.php?U=".htmlspecialchars($username, ENT_QUOTES)."&AC=".htmlspecialchars($hmacSignature, ENT_QUOTES);
	$body = "<B>Welcome New Horse Isle Member!</B><BR><BR>\r\nTo Activate your account, Click the following link,  or Copy-Paste/Type it in your browser.<BR><HR>\r\n<A HREF='".$activateUrl."'>\r\n".$activateUrl."</A><BR>\r\n or <BR>\r\n( ".$activateUrl." )\r\n<BR><HR>We hope you enjoy the game! Be sure you have written down your Username: ".htmlspecialchars($username, ENT_QUOTES)." and Password: ".htmlspecialchars($password, ENT_QUOTES)." someplace safe!<BR>\r\nNEVER give your password out to ANYONE, even someone claiming to work for Horse Isle.<BR>\r\n---------------------------------------------------------------------<BR>\r\n  Quick Start Guide: <BR>\r\n    #1) Log into ".get_host()." with your newly activated account.<BR>\r\n    #2) Join a server (Top one is recommended for new players)<BR>\r\n    #3) Once on server click ENTER WORLD. This will start up the game.<BR>\r\n                          Enjoy!<BR>\r\n";
	
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
	$headers .= 'From: '.$cfg['FROM_EMAIL_ADDR']."\r\n";
    $headers .= 'Reply-To: '.$cfg['FROM_EMAIL_ADDR']."\r\n";
    $headers .= 'X-Mailer: PHP/' . phpversion();
	
	$subject = "Horse Isle Account Verification";
	
	mail($email, $subject, $body, $headers);	
}


function count_topics(string $fourm)
{
	$connect = sql_connect();
	$stmt = $connect->prepare("SELECT COUNT(*) FROM FourmThread WHERE Fourm=?"); 
	$stmt->bind_param("s", $fourm);
	$stmt->execute();
	$result = $stmt->get_result();
	$count = intval($result->fetch_row()[0]);
	return $count;
}

function count_replies(int $thread)
{
	$connect = sql_connect();
	$stmt = $connect->prepare("SELECT COUNT(*) FROM FourmReply WHERE ThreadId=?"); 
	$stmt->bind_param("i", $thread);
	$stmt->execute();
	$result = $stmt->get_result();
	$count = intval($result->fetch_row()[0]);
	return $count;
}

function get_last_reply_author(string $thread)
{
	$connect = sql_connect();
	$stmt = $connect->prepare("SELECT * FROM FourmReply WHERE ThreadId=? ORDER BY CreationTime DESC LIMIT 1"); 
	$stmt->bind_param("i", $thread);
	$stmt->execute();
	$result = $stmt->get_result();
	$author = $result->fetch_row()[2];
	return $author;
}

function get_last_reply_time(string $thread)
{
	$connect = sql_connect();
	$stmt = $connect->prepare("SELECT * FROM FourmReply WHERE ThreadId=? ORDER BY CreationTime DESC LIMIT 1"); 
	$stmt->bind_param("i", $thread);
	$stmt->execute();
	$result = $stmt->get_result();
	$author = $result->fetch_row()[5];
	return $author;
}

function get_first_reply_author(string $thread)
{
	$connect = sql_connect();
	$stmt = $connect->prepare("SELECT * FROM FourmReply WHERE ThreadId=? ORDER BY CreationTime ASC LIMIT 1"); 
	$stmt->bind_param("i", $thread);
	$stmt->execute();
	$result = $stmt->get_result();
	$author = $result->fetch_row()[2];
	return $author;
}

function get_first_reply_time(string $thread)
{
	$connect = sql_connect();
	$stmt = $connect->prepare("SELECT * FROM FourmReply WHERE ThreadId=? ORDER BY CreationTime ASC LIMIT 1"); 
	$stmt->bind_param("i", $thread);
	$stmt->execute();
	$result = $stmt->get_result();
	$author = $result->fetch_row()[5];
	return $author;
}

function create_fourm_thread(string $title, string $fourm)
{
	$cfg = get_cfg(CFG_FILE);	
	$connect = sql_connect();
	$result = mysqli_query($connect, "SELECT MAX(ThreadId) FROM FourmThread");
	
	$thread_id = $result->fetch_row()[0] + 1;
	if($thread_id == NULL)
		$thread_id = 0;
	$curTime = time();

	$stmt = $connect->prepare("INSERT INTO FourmThread VALUES(?,?,?,?,'NO')"); 
	$stmt->bind_param("issi", $thread_id, $title, $fourm, $curTime);
	$stmt->execute();
	
	return $thread_id;
}


function set_thread_update(int $thread_id)
{
	$time = time();
	$connect = sql_connect();
	$stmt = $connect->prepare("UPDATE FourmThread SET UpdateTime=? WHERE ThreadId=?");
	$stmt->bind_param("ii", $time, $thread_id);
	$stmt->execute();
}

function create_fourm_reply(int $thread_id, string $username, string $contents, string $fourm, bool $madeByAdmin)
{
	$cfg = get_cfg(CFG_FILE);	
	$connect = sql_connect();
	$result = mysqli_query($connect, "SELECT MAX(ReplyId) FROM FourmReply");
	
	$reply_id = $result->fetch_row()[0] + 1;
	if($reply_id == NULL)
		$reply_id = 0;
	$curTime = time();

	if($madeByAdmin)
		$admin = "YES";
	else
		$admin = "NO";

	$stmt = $connect->prepare("INSERT INTO FourmReply VALUES(?,?,?,?,?,?,?)"); 
	$stmt->bind_param("iisssis", $reply_id, $thread_id, $username, $contents, $fourm, $curTime, $admin);
	$stmt->execute();
	
	set_thread_update($thread_id);
	
	return $reply_id;
}


function get_fourm_thread($threadId)
{
	$connect = sql_connect();
	$stmt = $connect->prepare("SELECT * FROM FourmThread WHERE ThreadId=?"); 
	$stmt->bind_param("i", $threadId);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_row();
	return ['id' => $row[0], 'title' => $row[1], 'fourm' => $row[2], 'creation_time' => $row[3], 'locked' => ($row[4] === "YES")];;
}

function get_fourm_replies($threadId)
{
	$connect = sql_connect();
	$stmt = $connect->prepare("SELECT * FROM FourmReply WHERE ThreadId=?"); 
	$stmt->bind_param("i", $threadId);
	$stmt->execute();
	$result = $stmt->get_result();
	$replies = [];
		

	while ($row = $result->fetch_row()) {
		$arr = [ ['reply_id' => $row[0], 'thread_id' => $row[1], 'author' => $row[2], 'contents' => $row[3], 'fourm' => $row[4], 'creation_time' => $row[5], 'admin' => ($row[6] === "YES")] ];
		$replies = array_merge($replies, $arr);
	}
	
	return $replies;
}


function get_all_news()
{
	$connect = sql_connect();
	$stmt = $connect->prepare("SELECT * FROM News ORDER BY CreationDate DESC"); 
	$stmt->execute();
	$result = $stmt->get_result();
	$news = [];
		

	while ($row = $result->fetch_row()) {
		$arr = [ ['id' => $row[0], 'date' => $row[1], 'title' => $row[2], 'contents' => $row[3]] ];
		$news = array_merge($news, $arr);
	}
	
	return $news;

}

function get_news_id(int $id)
{
	$connect = sql_connect();
	$stmt = $connect->prepare("SELECT * FROM News WHERE NewsId=?"); 
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$result = $stmt->get_result();

	$news = [];
		

	while ($row = $result->fetch_row()) {
		$arr = [ ['id' => $row[0], 'date' => $row[1], 'title' => $row[2], 'contents' => $row[3]] ];
		$news = array_merge($news, $arr);
	}
	
	return $news;
}


function get_recent_news()
{
	$connect = sql_connect();
	$stmt = $connect->prepare("SELECT * FROM News ORDER BY CreationDate DESC LIMIT 5"); 
	$stmt->execute();
	$result = $stmt->get_result();
	$news = [];
		

	while ($row = $result->fetch_row()) {
		$arr = [ ['id' => $row[0], 'date' => $row[1], 'title' => $row[2], 'contents' => $row[3]] ];
		$news = array_merge($news, $arr);
	}
	
	return $news;

}

function get_latest_news()
{
	$connect = sql_connect();
	$stmt = $connect->prepare("SELECT * FROM News ORDER BY CreationDate DESC LIMIT 1"); 
	$stmt->execute();
	$result = $stmt->get_result();
	$news = [];
		

	while ($row = $result->fetch_row()) {
		$arr = [ ['id' => $row[0], 'date' => $row[1], 'title' => $row[2], 'contents' => $row[3]] ];
		$news = array_merge($news, $arr);
	}
	
	return $news;

}


function post_news(string $title, string $text)
{
	$connect = sql_connect();
	$result = mysqli_query($connect, "SELECT MAX(NewsId) FROM News");
	
	$news_id = $result->fetch_row()[0] + 1;
	if($news_id == NULL)
		$news_id = 0;
	$curTime = time();

	$stmt = $connect->prepare("INSERT INTO News VALUES(?,?,?,?)"); 
	$stmt->bind_param("iiss", $news_id, time(), $title, nl2br($text));
	$stmt->execute();
}


function get_fourm_threads($fourm)
{
	$connect = sql_connect();
	$stmt = $connect->prepare("SELECT * FROM FourmThread WHERE Fourm=? ORDER BY UpdateTime DESC"); 
	$stmt->bind_param("s", $fourm);
	$stmt->execute();
	$result = $stmt->get_result();
	$threads = [];
		

	while ($row = $result->fetch_row()) {
		$arr = [ ['id' => $row[0], 'title' => $row[1], 'fourm' => $row[2], 'update_time' => $row[3], 'locked' => ($row[4] === "YES")] ];
		$threads = array_merge($threads, $arr);
	}
	
	return $threads;
}

function get_email(int $userid)
{
	$connect = sql_connect();
	$stmt = $connect->prepare("SELECT Email FROM Users WHERE Id=?"); 
	$stmt->bind_param("i", $userid);
	$stmt->execute();
	$result = $stmt->get_result();
	$email = $result->fetch_row()[0];
	return $email;
}

function get_userid(string $username)
{
	$connect = sql_connect();
	$usernameUppercase = strtoupper($username);
	$stmt = $connect->prepare("SELECT Id FROM Users WHERE UPPER(Username)=?"); 
	$stmt->bind_param("s", $usernameUppercase);
	$stmt->execute();
	$result = $stmt->get_result();
	$id = intval($result->fetch_row()[0]);
	return $id;
}

function get_sex(int $userid)
{
	$connect = sql_connect();
	
	$stmt = $connect->prepare("SELECT Gender FROM Users WHERE Id=?"); 
	$stmt->bind_param("i", $userid);
	$stmt->execute();
	$result = $stmt->get_result();
	return $result->fetch_row()[0];

}

function get_admin(int $userid)
{
	$connect = sql_connect();
	
	$stmt = $connect->prepare("SELECT Admin FROM Users WHERE Id=?"); 
	$stmt->bind_param("i", $userid);
	$stmt->execute();
	$result = $stmt->get_result();
	return $result->fetch_row()[0] === "YES";

}

function get_mod(int $userid)
{
	$connect = sql_connect();
	
	$stmt = $connect->prepare("SELECT Moderator FROM Users WHERE Id=?"); 
	$stmt->bind_param("i", $userid);
	$stmt->execute();
	$result = $stmt->get_result();
	return $result->fetch_row()[0] === "YES";

}

function get_password_hash(int $userid)
{
	$connect = sql_connect();
	$stmt = $connect->prepare("SELECT PassHash FROM Users WHERE Id=?"); 
	$stmt->bind_param("i", $userid);
	$stmt->execute();
	$result = $stmt->get_result();
	return $result->fetch_row()[0];
	
}

function get_salt(int $userid)
{
	$connect = sql_connect();	
	$stmt = $connect->prepare("SELECT Salt FROM Users WHERE Id=?"); 
	$stmt->bind_param("i", $userid);
	$stmt->execute();
	$result = $stmt->get_result();
	return $result->fetch_row()[0];
}

function check_password(int $userId, string $password)
{
	$passhash = get_password_hash($userId);
	$passsalt = hex2bin(get_salt($userId));
	$acturalhash = hash_salt($password, $passsalt);
	
	if($acturalhash === $passhash)
		return true;
	else
		return false;
}

function count_LastOn(int $userId)
{
	$connect = sql_connect();
	$stmt = $connect->prepare("SELECT COUNT(*) FROM LastOn WHERE Id=?");
	$stmt->bind_param("i", $userId);
	$stmt->execute();
	$result = $stmt->get_result();
	$v = $result->fetch_row();	
	return intval($v[0]);
}

function get_email_activation_status(int $userId)
{
	$connect = sql_connect();
	$stmt = $connect->prepare("SELECT EmailActivated FROM Users WHERE Id=?");
	$stmt->bind_param("i", $userId);
	$stmt->execute();
	$result = $stmt->get_result();
	$v = $result->fetch_row();
	
    
	return $v[0] === "YES";
}

function get_LastOn(int $userId)
{
	if(count_LastOn($userId) <= 0){
		return "NONE";
	}


	$connect = sql_connect();
	$stmt = $connect->prepare("SELECT ServerId FROM LastOn WHERE Id=?");
	$stmt->bind_param("i", $userId);
	$stmt->execute();
	$result = $stmt->get_result();
	$v = $result->fetch_row();
	
    
	return $v[0];
}

function set_LastOn(int $userId, string $lastOn)
{
	$cfg = get_cfg(CFG_FILE);	
	if(get_LastOn($userId) === "NONE")
	{
		$connect = sql_connect();
		$stmt = $connect->prepare("INSERT INTO LastOn VALUES(?, ?)");
		$stmt->bind_param("is", $userId, $lastOn);
		$stmt->execute();
	}
	else
	{
		$connect = sql_connect();
		$stmt = $connect->prepare("UPDATE LastOn SET ServerId=? WHERE Id=?");
		$stmt->bind_param("si", $lastOn, $userId);
		$stmt->execute();
	}
}

function populate_db()
{
	
	$connect = sql_connect();
	mysqli_query($connect, "CREATE TABLE IF NOT EXISTS Users(Id INT, Username TEXT(16),Email TEXT(128),Country TEXT(128),SecurityQuestion Text(128),SecurityAnswerHash TEXT(128),Age INT,PassHash TEXT(128), Salt TEXT(128),Gender TEXT(16), Admin TEXT(3), Moderator TEXT(3), EmailActivated TEXT(3))");
	mysqli_query($connect, "CREATE TABLE IF NOT EXISTS LastOn(Id INT, ServerId TEXT(1028))");
	mysqli_query($connect, "CREATE TABLE IF NOT EXISTS FourmThread(ThreadId INT, Title TEXT(100), Fourm TEXT(10), UpdateTime INT, Locked TEXT(3))");
	mysqli_query($connect, "CREATE TABLE IF NOT EXISTS FourmReply(ReplyId INT, ThreadId INT, CreatedBy TEXT(1028), Contents TEXT(65565), Fourm TEXT(10), CreationTime INT, MadeByAdmin TEXT(3))");
	mysqli_query($connect, "CREATE TABLE IF NOT EXISTS News(NewsId INT, CreationDate INT, Title TEXT(1028), Contents TEXT(65565))");
}

function startsWith( $haystack, $needle ) {
     $length = strlen( $needle );
     return substr( $haystack, 0, $length ) === $needle;
}

function endsWith( $haystack, $needle ) {
    $length = strlen( $needle );
    if( !$length ) {
        return true;
    }
    return substr( $haystack, -$length ) === $needle;
}

function getServerById(string $id)
{
	$server_list = get_servers();
	for($i = 0; $i < count($server_list); $i++)
	{
		if($server_list[$i]['id'] == $id){
			return $server_list[$i];			
		}
	}
	return null;
}


?>