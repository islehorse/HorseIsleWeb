<?php
// Excuse my really shitty php code here.

$number = 0;
if(isset($_GET["I"])){
	$number = intval($_GET["I"]);
}
$artistsJson = file_get_contents("artists.json");
$artistData = json_decode($artistsJson);
$totalArts = count($artistData);
if($number >= 0 && $number < $totalArts)
{
	$artistThis = $artistData[$number];
	$artistDesc = $artistThis->desc;
	$artistName = $artistThis->name;
	$artistServer = $artistThis->server;
	$artistContent = $artistThis->content;
}
else
{
	$artistDesc = "";
	$artistName = "";
	$artistServer = "";
	$artistContent = "";
}

if($number == 0){
	echo("<BODY BGCOLOR=B0B0B0><FONT FACE=ARIAL><CENTER><HR>Horse Isle: Secret Land of Horses fan art submission winners!  The following are in no particular order whatsoever.<BR>We also added some corney blurbage to the top of each work.  Feel free to ignore that :)   -- Enjoy!<HR><B>[<A HREF='/'>HOME</A>]</B> <B>[<A HREF='?I=".(string)($number+1)."'>NEXT</A>]</B> <BR>(".(string)($number+1)." of ".(string)$totalArts.") Player: ".htmlspecialchars($artistName)." &nbsp; Main Server: ".htmlspecialchars($artistServer).".<BR><FONT SIZE=+1><B><I>".htmlspecialchars($artistDesc)."</I></B></FONT><BR>".$artistContent."<BR><FONT SIZE=-2>Copyright &copy; 2009 HorseIsle and the respective artist of each work.");
}
else if($number == $totalArts-1){
	echo("<BODY BGCOLOR=B0B0B0><FONT FACE=ARIAL><CENTER><B>[<A HREF='?I=".(string)($number-1)."'>BACK</A>]</B> <B>[<A HREF='/'>HOME</A>]</B> <BR>(".(string)($number+1)." of ".(string)$totalArts.") Player: ".htmlspecialchars($artistName)." &nbsp; Main Server: ".htmlspecialchars($artistServer).".<BR><FONT SIZE=+1><B><I>".htmlspecialchars($artistDesc)."</I></B></FONT><BR>".$artistContent."<BR><FONT SIZE=-2>Copyright &copy; 2009 HorseIsle and the respective artist of each work.");
}
else{
	echo("<BODY BGCOLOR=B0B0B0><FONT FACE=ARIAL><CENTER><B>[<A HREF='?I=".(string)($number-1)."'>BACK</A>]</B> <B>[<A HREF='/'>HOME</A>]</B> <B>[<A HREF='?I=".(string)($number+1)."'>NEXT</A>]</B> <BR>(".(string)($number+1)." of ".(string)$totalArts.") Player: ".htmlspecialchars($artistName)." &nbsp; Main Server: ".htmlspecialchars($artistServer).".<BR><FONT SIZE=+1><B><I>".htmlspecialchars($artistDesc)."</I></B></FONT><BR>".$artistContent."<BR><FONT SIZE=-2>Copyright &copy; 2009 HorseIsle and the respective artist of each work.");
}
?>