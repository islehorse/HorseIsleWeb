<?php

function getPlayerList($database)
{
	$connect = sql_connect($database);
	$onlineUsers = mysqli_query($connect, "SELECT * FROM OnlineUsers");
	
	$users_on = [];
		

	while ($row = $onlineUsers->fetch_row()) {
		$arr = [ ['id' => $row[0], 'admin' => ($row[1] == 'YES'), 'mod' => ($row[2] == 'YES'), 'subbed' => ($row[3] == 'YES'), 'new' => ($row[4] == 'YES')] ];
		$users_on = array_merge($users_on, $arr);
	}
	
	return $users_on;
}

function checkUserBuddy($database, $yourId, $friendsId)
{
	$connect = sql_connect($database);
	$stmt = $connect->prepare("SELECT COUNT(1) FROM BuddyList WHERE (Id=? AND IdFriend=?) OR (Id=? AND IdFriend=?)");
	$stmt->bind_param("iiii", $yourId, $friendsId, $friendsId, $yourId);
	$stmt->execute();
	$result = $stmt->get_result();
	return $result->fetch_row()[0];
}


function getNoPlayersOnlineInServer($database)
{
	$connect = sql_connect($database);
	$onlineUsers = mysqli_query($connect, "SELECT COUNT(1) FROM OnlineUsers");
	return $onlineUsers->fetch_row()[0];
}

function getNoSubbedPlayersOnlineInServer($database)
{
	$connect = sql_connect($database);
	$onlineSubscribers = mysqli_query($connect, "SELECT COUNT(1) FROM OnlineUsers WHERE Subscribed = 'YES'");
	return $onlineSubscribers->fetch_row()[0];
}

function getUserMoney($database, $id)
{
	$connect = sql_connect($database);
	$stmt = $connect->prepare("SELECT Money FROM UserExt WHERE Id=?");
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$result = $stmt->get_result();
	
	$rows = $result->fetch_row();
	if($rows == NULL) return 0;
	
	return intval($rows[0]);
	
}

function setUserMoney($database, $id, $money)
{
	$connect = sql_connect($database);
	$stmt = $connect->prepare("UPDATE UserExt SET Money=? WHERE Id=?");
	$stmt->bind_param("ii", $money, $id);
	$stmt->execute();
}

function setUserSubbed($database, $id, $subbed)
{
	$subedV = "";
	if($subbed)
		$subedV = "YES";
	else
		$subbedV = "NO";
	
	$connect = sql_connect($database);
	$stmt = $connect->prepare("UPDATE UserExt SET Subscriber=? WHERE Id=?");
	$stmt->bind_param("si", $subedV, $id);
	$stmt->execute();
}

function setUserSubbedUntil($database, $id, $subbedUntil)
{
	$connect = sql_connect($database);
	$stmt = $connect->prepare("UPDATE UserExt SET SubscribedUntil=? WHERE Id=?");
	$stmt->bind_param("ii", $subbedUntil, $id);
	$stmt->execute();
}

function getUserBankMoney($database, $id)
{
	$connect = sql_connect($database);
	$stmt = $connect->prepare("SELECT BankBalance FROM UserExt WHERE Id=?");
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$result = $stmt->get_result();
	
	return intval($result->fetch_row()[0]);
	
}

function getUserLoginDate($database, $id)
{
	$connect = sql_connect($database);
	$stmt = $connect->prepare("SELECT LastLogin FROM UserExt WHERE Id=?");
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$result = $stmt->get_result();
	
	return intval($result->fetch_row()[0]);
	
}

function getUserQuestPoints($database, $id)
{
	$connect = sql_connect($database);
	$stmt = $connect->prepare("SELECT QuestPoints FROM UserExt WHERE Id=?");
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$result = $stmt->get_result();
	
	return intval($result->fetch_row()[0]);
	
}

function getUserExistInExt($database, $id)
{
	$connect = sql_connect($database);
	$stmt = $connect->prepare("SELECT COUNT(*) FROM UserExt WHERE Id=?");
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$result = $stmt->get_result();
	
	return intval($result->fetch_row()[0]) >= 1;
	
}

function getUserTotalLogins($database, $id)
{
	$connect = sql_connect($database);
	$stmt = $connect->prepare("SELECT TotalLogins FROM UserExt WHERE Id=?");
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$result = $stmt->get_result();
	
	return intval($result->fetch_row()[0]);
	
}

function getUserPlaytime($database, $id)
{
	$connect = sql_connect($database);
	$stmt = $connect->prepare("SELECT FreeMinutes FROM UserExt WHERE Id=?");
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$result = $stmt->get_result();
	
	return intval($result->fetch_row()[0]);
	
}


function getUserSubTimeRemaining($database, $id)
{
	$connect = sql_connect($database);
	$stmt = $connect->prepare("SELECT SubscribedUntil FROM UserExt WHERE Id=?");
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$result = $stmt->get_result();
	
	return intval($result->fetch_row()[0]);
	
}


function addItemToPuchaseQueue($database, $playerId, $itemId, $itemCount)
{
	$connect = sql_connect($database);
	$stmt = $connect->prepare("INSERT INTO ItemPurchaseQueue VALUES(?,?,?)");
	$stmt->bind_param("iii", $playerId, $itemId, $itemCount);
	$stmt->execute();
	$result = $stmt->get_result();
	mysqli_close($connect);
}

function getUserSubbed($database, $id)
{
	$connect = sql_connect($database);
	$stmt = $connect->prepare("SELECT Subscriber FROM UserExt WHERE Id=?");
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$result = $stmt->get_result();	
	$subbed =  $result->fetch_row()[0] == "YES";
	mysqli_close($connect);

	return $subbed;
}

function isUserOnline($database, $id)
{
	$connect = sql_connect($database);
	$stmt = $connect->prepare("SELECT COUNT(1) FROM OnlineUsers WHERE playerId=?");
	$stmt->bind_param("i", $userid);
	$stmt->execute();
	$result = $stmt->get_result();
	$count = intval($result->fetch_row()[0]);
	mysqli_close($connect);

	return $count>0;	
}

function getNoModPlayersOnlineInServer($database)
{
	$connect = sql_connect($database);
	$onlineModerators = mysqli_query($connect, "SELECT COUNT(1) FROM OnlineUsers WHERE Moderator = 'YES' OR Admin='YES'");
	$num = $onlineModerators->fetch_row()[0];
	mysqli_close($connect);
	return $num;
}


function userid_exists(string $database, string $userid)
{
	$connect = sql_connect($database);
	$stmt = $connect->prepare("SELECT COUNT(1) FROM Users WHERE Id=?");
	$stmt->bind_param("i", $userid);
	$stmt->execute();
	$result = $stmt->get_result();
	$count = intval($result->fetch_row()[0]);
	mysqli_close($connect);

	return $count>0;
}

function createAccountOnServer(string $database)
{

	$id = intval($_SESSION['PLAYER_ID']);
	$username = $_SESSION['USERNAME'];
	$sex = $_SESSION['SEX'];
	$admin = $_SESSION['ADMIN'];
	$mod = $_SESSION['MOD'];
	$passhash = $_SESSION['PASSWORD_HASH'];
	$salt = $_SESSION['SALT'];


	$connect = sql_connect($database);
	$stmt = $connect->prepare("INSERT INTO Users VALUES(?,?,?,?,?,?,?)"); 
	$stmt->bind_param("issssss", $id, $username, $passhash, $salt, $sex, $admin, $mod);
	$stmt->execute();
	mysqli_close($connect);
}

# Global Functions
function getNoPlayersOnlineGlobal()
{
	$server_list = get_servers();
	$playersOn = 0;
	for($i = 0; $i < count($server_list); $i++)
	{
		$playersOn += getNoPlayersOnlineInServer($server_list[$i]['database']);
	}
	return $playersOn;
}

function userExistAny($playerId)
{
	$server_list = get_servers();
	for($i = 0; $i < count($server_list); $i++)
	{
		if(userid_exists($server_list[$i]['database'], $playerId)){
			return true;
		}
	}
	return false;
}


function getNoSubbedPlayersOnlineGlobal()
{
	$server_list = get_servers();
	$playersOn = 0;
	for($i = 0; $i < count($server_list); $i++)
	{
		$playersOn += getNoSubbedPlayersOnlineInServer($server_list[$i]['database']);
	}
	return $playersOn;
}

function getNoModPlayersOnlineGlobal()
{
	$server_list = get_servers();
	$playersOn = 0;
	for($i = 0; $i < count($server_list); $i++)
	{
		$playersOn += getNoModPlayersOnlineInServer($server_list[$i]['database']);
	}
	return $playersOn;
}


?>
