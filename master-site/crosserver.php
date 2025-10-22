<?php
$server_list = get_servers();

function getPlayerList(string $serverId)
{
	return api_send($serverId, "player_list", []);
}

function checkUserBuddy(string $serverId)
{
	return api_send($serverId, "check_buddy", [$yourId, $friendsId]);
}

function getNoPlayersOnlineInServer(string $serverId)
{
	return api_send($serverId, "get_num_players_online", []);
}

function getNoSubbedPlayersOnlineInServer(string $serverId)
{
	return api_send($serverId, "get_num_subbed_players_online", []);
}

function getUserMoney(string $serverId, $id)
{
	return api_send($serverId, "get_user_money", [$id]);	
}

function setUserMoney(string $serverId, $id, $money)
{
	return api_send($serverId, "set_user_money", [$id, $money]);	
}

function setUserSubbed(string $serverId, $id, $subbed)
{
	return api_send($serverId, "set_user_subbed", [$id, $subbed]);	
}

function setUserSubbedUntil(string $serverId, $id, $subbedUntil)
{
	return api_send($serverId, "set_user_subbed_until", [$id, $subbedUntil]);
}

function getUserBankMoney(string $serverId, $id)
{
	return api_send($serverId, "get_bank_money", [$id]);	
}

function getUserLoginDate(string $serverId, $id)
{
	return api_send($serverId, "get_user_login_date", [$id]);
}

function getUserQuestPoints(string $serverId, $id)
{
	return api_send($serverId, "get_user_quest_points", [$id]);
}

function getUserExistInExt(string $serverId, $id)
{
	return api_send($serverId, "get_user_in_userext", [$id]);	
}

function getUserTotalLogins(string $serverId, $id)
{
	return api_send($serverId, "get_user_total_logins", [$id]);		
}

function getUserPlaytime(string $serverId, $id)
{
	return api_send($serverId, "get_user_playtime", [$id]);			
}

function getUserSubTimeRemaining(string $serverId, $id)
{
	return api_send($serverId, "get_user_sub_time_remaining", [$id]);	
}

function addItemToPuchaseQueue(string $serverId, $playerId, $itemId, $itemCount)
{
	return api_send($serverId, "add_item_to_purchase_queue", [$playerId, $itemId, $itemCount]);	
}

function getUserSubbed(string $serverId, $id)
{
	return api_send($serverId, "get_user_subbed", [$id]);	
}

function isUserOnline(string $serverId, $id)
{
	return api_send($serverId, "is_user_online", [$id]);
}

function getNoModPlayersOnlineInServer(string $serverId)
{
	return api_send($serverId, "get_num_mods_online", []);
}

function checkUserIdExist(string $serverId, int $userid)
{
	return api_send($serverId, "userid_exist", []);
}

function createAccountOnServer(string $serverId, int $id, string $username, string $sex, string $admin, string $mod, string $passhash, string $salt)
{
	return api_send($serverId, "create_account_on_server", [$id, $username, $sex, $admin, $mod, $passhash, $salt]);
}

# Global Functions
function getNoPlayersOnlineGlobal()
{
	$server_list = get_servers();
	$playersOn = 0;
	for($i = 0; $i < count($server_list); $i++)
	{
		$playersOn += getNoPlayersOnlineInServer($server_list[$i]['id']);
	}
	return $playersOn;
}

function userExistAny($playerId)
{
	$server_list = get_servers();
	for($i = 0; $i < count($server_list); $i++)
	{
		if(checkUserIdExist($server_list[$i]['id'], $playerId)){
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
		$playersOn += getNoSubbedPlayersOnlineInServer($server_list[$i]['id']);
	}
	return $playersOn;
}

function getNoModPlayersOnlineGlobal()
{
	$server_list = get_servers();
	$playersOn = 0;
	for($i = 0; $i < count($server_list); $i++)
	{
		$playersOn += getNoModPlayersOnlineInServer($server_list[$i]['id']);
	}
	return $playersOn;
}


?>
