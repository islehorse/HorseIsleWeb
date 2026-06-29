<?php
include("../common.php");
include("../api.php");
$gcfg = get_cfg_game();

$id = -1;
if(isset($_GET['id'])){
	$id = intval($_GET['id']);
}
$on = getPlayerList();
$numbOn = count($on);
$budsOn = 0;
?>
<B><?php echo($numbOn)?> players<BR>online now:</B><?php

for($i = 0; $i < $numbOn; $i++){
	$name = getUserName($on[$i]['id']);
	$admin = $on[$i]['admin'];
	$mod = $on[$i]['mod'];
	$subbed = $on[$i]['subbed'];
	$new = $on[$i]['new'];
	$bud = checkUserBuddy($id ,$on[$i]['id']);
	
	echo("<BR>");
	if($bud) { echo('<B><FONT COLOR=BLUE>'); echo(htmlspecialchars($name)); echo('</FONT></B>'); $budsOn++; }
	else if($admin) { echo('<B><FONT COLOR=RED>'); echo(htmlspecialchars($name)); echo('</FONT></B>'); }
	else if($mod) { echo('<B><FONT COLOR=GREEN>'); echo(htmlspecialchars($name)); echo('</FONT></B>'); }	
	else { echo(htmlspecialchars($name)); }
	
	if($new) { echo(' <FONT SIZE=-2 COLOR=660000>[new]</FONT>'); };
	
}
?><BR><I><FONT COLOR=BLUE>(<?php echo($budsOn); ?> buddies)</FONT></I><BR><FONT COLOR=222222 SIZE=-1><I>This list refreshes every 30 seconds.</I></FONT>