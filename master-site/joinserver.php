<?php
session_start();
include('common.php');
include('crosserver.php');

if(isset($_GET['SERVER']))
{
	$server_id = $_GET['SERVER'];
	$server = getServerById($server_id);
	
	if($server !== null)
	{
		if(is_logged_in())
		{
			$playerId = $_SESSION['PLAYER_ID'];
			if(!checkUserIdExist($server['id'], $playerId))
			{				
				createAccountOnServer($server['id'],
									  intval($_SESSION['PLAYER_ID']),
									  $_SESSION['USERNAME'],
									  $_SESSION['SEX'],
									  $_SESSION['ADMIN'],
									  $_SESSION['MOD'],
									  $_SESSION['PASSWORD_HASH'],
									  $_SESSION['SALT']);
				
				$hmac = GenHmacMessage((string)$playerId, "CrossSiteLogin");
				$redirectUrl = $server['external_site'];
				
				if(!endsWith($redirectUrl, '/'))
					$redirectUrl .= '/';
				
				$redirectUrl .= 'account.php?SLID='.(string)$playerId.'&C='.base64_encode(hex2bin($hmac));
				set_LastOn($playerId, $server_id);
				
				header("Location: ".$redirectUrl);
				exit();
			}
			else
			{
				echo('[Account]Joining the Server Failed.  Please try a different server,  or Try re-logging into the website.  If you continue to have troubles, you may need to enable Cookies in your browser.  Another possibility ONLY if you already have an account is logging directly into the server via: '.$server['external_site'].'<BR>ERROR: Account is already setup on this server. / <HR><B>If you already have an account on server, try logging in direct: <A HREF=\''.$server['external_site'].'\'>'.$server['external_site'].'</A></B>');
			}
		}
		else
		{
			echo('[Account]Joining the Server Failed.  Please try a different server,  or Try re-logging into the website.  If you continue to have troubles, you may need to enable Cookies in your browser.  Another possibility ONLY if you already have an account is logging directly into the server via: '.$server['external_site'].'/<BR>ERROR: Account Setup Failed.  Please be sure you are logged in. / <HR><B>If you already have an account on server, try logging in direct: <A HREF=\''.$server['external_site'].'/\'>'.$server['external_site'].'</A></B>');
		}
	}
	else
	{
		echo('[]Joining the Server Failed.  Please try a different server,  or Try re-logging into the website.  If you continue to have troubles, you may need to enable Cookies in your browser.  Another possibility ONLY if you already have an account is logging directly into the server via: <BR>ERROR:  / The requested URL returned error: 404 Not Found<HR><B>If you already have an account on server, try logging in direct: </B>');
	}
}
?>