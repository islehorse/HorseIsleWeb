<?php
if(!function_exists('is_logged_in'))
	include("common.php");

function getPlayerList()
{
	$connect = sql_connect();
	$onlineUsers = mysqli_query($connect, "SELECT * FROM OnlineUsers");
	
	$users_on = [];
		

	while ($row = $onlineUsers->fetch_row()) {
		$arr = [ ['id' => $row[0], 'admin' => ($row[1] == 'YES'), 'mod' => ($row[2] == 'YES'), 'subbed' => ($row[3] == 'YES'), 'new' => ($row[4] == 'YES')] ];
		$users_on = array_merge($users_on, $arr);
	}
	
	return $users_on;
}

function checkUserBuddy($yourId, $friendsId)
{
	$connect = sql_connect();
	$stmt = $connect->prepare("SELECT COUNT(1) FROM BuddyList WHERE (Id=? AND IdFriend=?) OR (Id=? AND IdFriend=?)");
	$stmt->bind_param("iiii", $yourId, $friendsId, $friendsId, $yourId);
	$stmt->execute();
	$result = $stmt->get_result();
	return $result->fetch_row()[0];
}


function getNoPlayersOnlineInServer()
{
	$connect = sql_connect();
	$onlineUsers = mysqli_query($connect, "SELECT COUNT(1) FROM OnlineUsers");
	return $onlineUsers->fetch_row()[0];
}

function getNoSubbedPlayersOnlineInServer()
{
	$connect = sql_connect();
	$onlineSubscribers = mysqli_query($connect, "SELECT COUNT(1) FROM OnlineUsers WHERE Subscribed = 'YES'");
	return $onlineSubscribers->fetch_row()[0];
}

function getUserMoney($id)
{
	$connect = sql_connect();
	$stmt = $connect->prepare("SELECT Money FROM UserExt WHERE Id=?");
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$result = $stmt->get_result();
	
	$rows = $result->fetch_row();
	if($rows == NULL) return 0;
	
	return intval($rows[0]);
	
}

function setUserMoney($id, $money)
{
	$connect = sql_connect();
	$stmt = $connect->prepare("UPDATE UserExt SET Money=? WHERE Id=?");
	$stmt->bind_param("ii", $money, $id);
	$stmt->execute();
}

function setUserSubbed($id, $subbed)
{
	$subedV = "";
	if($subbed)
		$subedV = "YES";
	else
		$subbedV = "NO";
	
	$connect = sql_connect();
	$stmt = $connect->prepare("UPDATE UserExt SET Subscriber=? WHERE Id=?");
	$stmt->bind_param("si", $subedV, $id);
	$stmt->execute();
}

function setUserSubbedUntil($id, $subbedUntil)
{
	$connect = sql_connect();
	$stmt = $connect->prepare("UPDATE UserExt SET SubscribedUntil=? WHERE Id=?");
	$stmt->bind_param("ii", $subbedUntil, $id);
	$stmt->execute();
}

function getUserBankMoney($id)
{
	$connect = sql_connect();
	$stmt = $connect->prepare("SELECT BankBalance FROM UserExt WHERE Id=?");
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$result = $stmt->get_result();
	
	return intval($result->fetch_row()[0]);
	
}

function getUserLoginDate($id)
{
	$connect = sql_connect();
	$stmt = $connect->prepare("SELECT LastLogin FROM UserExt WHERE Id=?");
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$result = $stmt->get_result();
	
	return intval($result->fetch_row()[0]);
	
}

function getUserQuestPoints($id)
{
	$connect = sql_connect();
	$stmt = $connect->prepare("SELECT QuestPoints FROM UserExt WHERE Id=?");
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$result = $stmt->get_result();
	
	return intval($result->fetch_row()[0]);
	
}

function getUserExistInExt($id)
{
	$connect = sql_connect();
	$stmt = $connect->prepare("SELECT COUNT(*) FROM UserExt WHERE Id=?");
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$result = $stmt->get_result();
	
	return intval($result->fetch_row()[0]) >= 1;
	
}

function getUserTotalLogins($id)
{
	$connect = sql_connect();
	$stmt = $connect->prepare("SELECT TotalLogins FROM UserExt WHERE Id=?");
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$result = $stmt->get_result();
	
	return intval($result->fetch_row()[0]);
	
}

function getUserPlaytime($id)
{
	$connect = sql_connect();
	$stmt = $connect->prepare("SELECT FreeMinutes FROM UserExt WHERE Id=?");
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$result = $stmt->get_result();
	
	return intval($result->fetch_row()[0]);
	
}


function getUserSubTimeRemaining($id)
{
	$connect = sql_connect();
	$stmt = $connect->prepare("SELECT SubscribedUntil FROM UserExt WHERE Id=?");
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$result = $stmt->get_result();
	
	return intval($result->fetch_row()[0]);
	
}


function addItemToPuchaseQueue($playerId, $itemId, $itemCount)
{
	$connect = sql_connect();
	$stmt = $connect->prepare("INSERT INTO ItemPurchaseQueue VALUES(?,?,?)");
	$stmt->bind_param("iii", $playerId, $itemId, $itemCount);
	$stmt->execute();
	$result = $stmt->get_result();
	mysqli_close($connect);
}

function getUserSubbed($id)
{
	$connect = sql_connect();
	$stmt = $connect->prepare("SELECT Subscriber FROM UserExt WHERE Id=?");
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$result = $stmt->get_result();	
	$subbed =  $result->fetch_row()[0] == "YES";
	mysqli_close($connect);

	return $subbed;
}

function isUserOnline($id)
{
	$connect = sql_connect();
	$stmt = $connect->prepare("SELECT COUNT(1) FROM OnlineUsers WHERE playerId=?");
	$stmt->bind_param("i", $userid);
	$stmt->execute();
	$result = $stmt->get_result();
	$count = intval($result->fetch_row()[0]);
	mysqli_close($connect);

	return $count>0;	
}

function getNoModPlayersOnlineInServer()
{
	$connect = sql_connect();
	$onlineModerators = mysqli_query($connect, "SELECT COUNT(1) FROM OnlineUsers WHERE Moderator = 'YES' OR Admin='YES'");
	$num = $onlineModerators->fetch_row()[0];
	mysqli_close($connect);
	return $num;
}


function checkUserIdExist(int $userid)
{
	$connect = sql_connect();
	$stmt = $connect->prepare("SELECT COUNT(1) FROM Users WHERE Id=?");
	$stmt->bind_param("i", $userid);
	$stmt->execute();
	$result = $stmt->get_result();
	$count = intval($result->fetch_row()[0]);
	mysqli_close($connect);

	return $count>0;
}

function createAccountOnServer(int $id, string $username, string $sex, string $admin, string $mod, string $passhash, string $salt)
{
	$connect = sql_connect();
	$stmt = $connect->prepare("INSERT INTO Users VALUES(?,?,?,?,?,?,?)"); 
	$stmt->bind_param("issssss", $id, $username, $passhash, $salt, $sex, $admin, $mod);
	$stmt->execute();
	mysqli_close($connect);
}

if(isset($_GET["req"], $_GET["data"], $_GET["hmac"])) {
	$req = $_GET["req"];

	$gothmac = $_GET["hmac"];
	$exphmac = GenHmacMessage($req.$_GET["data"], "HORSEISLE-CROSSERVER-REQUEST", false);
	
	
	if($gothmac === $exphmac) {
		header("Content-Type: application/json");
		$data = json_decode(base64_url_decode($_GET["data"]));
		
		switch($req) {
			case "player_list":
				echo(json_encode(getPlayerList()));
				break;
			case "check_buddy":
				echo(json_encode(checkUserBuddy($data[0], $data[1])));
				break;
			case "get_num_players_online":
				echo(json_encode(getNoPlayersOnlineInServer()));
				break;
			case "get_num_subbed_players_online":
				echo(json_encode(getNoSubbedPlayersOnlineInServer()));
				break;
			case "get_user_money":
				echo(json_encode(getUserMoney($data[0])));
				break;
			case "set_user_money":
				echo(json_encode(setUserMoney($data[0], $data[1])));
				break;
			case "set_user_subbed":
				echo(json_encode(setUserSubbed($data[0], $data[1])));
				break;
			case "set_user_subbed_until":
				echo(json_encode(setUserSubbedUntil($data[0], $data[1])));
				break;
			case "get_bank_money":
				echo(json_encode(getUserBankMoney($data[0])));
				break;
			case "get_user_login_date":
				echo(json_encode(getUserLoginDate($data[0])));
				break;
			case "get_user_quest_points":
				echo(json_encode(getUserQuestPoints($data[0])));
				break;
			case "get_user_in_userext":
				echo(json_encode(getUserExistInExt($data[0])));
				break;
			case "get_user_total_logins":
				echo(json_encode(getUserTotalLogins($data[0])));
				break;
			case "get_user_playtime":
				echo(json_encode(getUserPlaytime($data[0])));
				break;
			case "get_user_sub_time_remaining":
				echo(json_encode(getUserSubTimeRemaining($data[0])));
				break;
			case "add_item_to_purchase_queue":
				echo(json_encode(addItemToPuchaseQueue($data[0], $data[1], $data[2])));				
				break;
			case "get_user_subbed":
				echo(json_encode(getUserSubbed($data[0])));	
				break;
			case "is_user_online":
				echo(json_encode(isUserOnline($data[0])));
				break;
			case "get_num_mods_online":
				echo(json_encode(getNoModPlayersOnlineInServer()));
				break;
			case "userid_exist":
				echo(json_encode(checkUserIdExist($data[0])));
				break;
			case "create_account_on_server":
				echo(json_encode(createAccountOnServer($data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6])));
				break;
			default:
				error_log("invalid api method");
				echo(json_encode("error: invalid api method"));
				break;
		}
		
	}
	else {
		error_log("invalid hmac");
		header("Status: 403 Forbidden");
	}
}


?>