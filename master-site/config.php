<?php
$dbname = 'master';
$dbuser = 'root';
$dbpass = 'test123';
$dbhost = '127.0.0.1';

$pp_uri = '[GAMESITE]/web/ppemu.php'; # location of paypal emulator on game-servers
# [GAMESITE] is replaced with the URL for the game-site, as specified in servers.php
# original is https://www.paypal.com/cgi-bin/webscr which obviously wont do
# Dont set it to that though, as the paypalgateway.php is not implemented.

#should be same as all game-site's
$hmac_secret = 'c81f9522b9ecd84ad95af26d845a78d25208861555d4b18bf707eccf7b839d7c4cd635a38167552418e26838745336e7';
?>