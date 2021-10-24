<?php
$server_ip = '127.0.0.1';
$server_port = 12321;

$dbname = 'game1';
$dbuser = 'root';
$dbpass = 'test123';
$dbhost = '127.0.0.1';

$all_users_subbed = false;
$server_id = "game1";
$pp_uri = '/web/ppemu.php'; # location of paypal emulator on game-servers
# original is https://www.paypal.com/cgi-bin/webscr which obviously wont do
# Dont set it to that though, as the paypalgateway.php is not implemented.
 
$EXHANGE_RATE = 100000; # How much 1 USD is worth as HI Money

# == hmac_secret ==
# Used for master-site to communicate with game-sites,
# Should be set to the same value on all game sites and the master site.
# NOTE: if someone knows this secret they can login to ANYONES account
# Ideally, this would be a random string of numbers, letters and symbols like 20 characters long T-T
$hmac_secret = "c81f9522b9ecd84ad95af26d845a78d25208861555d4b18bf707eccf7b839d7c4cd635a38167552418e26838745336e7";
$master_site = "//localhost:80";
?>
