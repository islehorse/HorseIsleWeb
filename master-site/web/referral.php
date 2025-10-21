<?php
include("../common.php");
include("header.php");

if(session_status() !== PHP_SESSION_ACTIVE)
	session_start();

?>
<TABLE WIDTH=100% CELLPADDING=10><TR><TD>
<FONT COLOR=880000 SIZE=+1><B>Referring others to Horse Isle</B></FONT><BR>
In order to keep adding features to this game, we need subscribers.<BR> 
You are our best hope!  The following are links that you can email to friends who may be interested, or post on your favorite forums.<BR>
If a player signs up using your link, with your name in it, you will get the following benefits:<BR>
For each <B>month membership they buy for $5 you get 1 HorseBuck</B>. <I>(non-refundable credit towards game 1horsebuck=$1usd)</I><BR>
For each <B>yearly membership they buy for $40 you get 8 HorseBucks</B>. <I>(non-refundable credit towards game 8horsebucks=$8usd)</I><BR>
So, if you refer 5 players who subscribe and continue to subscribe, you will be able to play for free!<BR>
<BR><?php if(is_logged_in()) {
	echo('<FONT COLOR=440044 SIZE=+0>
<CENTER><I>Here are various linking codes to Horse Isle that will credit your account when used to sign up.</I></CENTER><BR>
<B>Direct Referral Web Address (for sending in an email to someone, etc.):</B>  
<BR><TT>'. get_protocol() . $host.'/?R='.$_SESSION['USERNAME'].'</TT><BR>
<HR>
<B>Web Page Link (for copy-pasting into website html code):</B> 
<BR><TT>&lt;a href="' . get_protocol() . $host . '/?R='.$_SESSION['USERNAME'].'">Horse Isle&lt;/a></TT><BR> 
<HR>
<B>BBCode Link (for copy-pasting into a bbcode supporting Forum):</B> 
<BR><TT>[url=' . get_protocol() . $host . '/?R='.$_SESSION['USERNAME'].']Horse Isle[/url]</TT><BR>
<HR>
<IMG SRC=/web/referral/referral1.gif>
<BR><B>Logo Image Link #1 (for copy pasting into website html or a forum that allows html):</B> 
<BR><TT>&lt;a href="' . get_protocol() . $host . '/?R='.$_SESSION['USERNAME'].'">
<BR>&lt;img border=0 src=' . get_protocol() . $host . '/web/referral/referral1.gif>&lt;/A></TT>
<HR>
<IMG SRC=/web/referral/referral2.gif>
<BR><B>Banner Image Link #2 (for copy pasting into website html or a forum that allows html):</B> 
<BR><TT>&lt;a href="' . get_protocol() . $host . '/?R='.$_SESSION['USERNAME'].'">
<BR>&lt;img border=0 src=' . get_protocol() . $host . '/web/referral/referral2.gif>&lt;/A></TT>
<HR>
<IMG SRC=/web/referral/referral3.gif>
<BR><B>Mini Image Link #3 (for copy pasting into website html or a forum that allows html):</B> 
<BR><TT>&lt;a href="' . get_protocol() . $host . '/?R='.$_SESSION['USERNAME'].'">
<BR>&lt;img border=0 src=' . get_protocol() . $host . '/web/referral/referral3.gif>&lt;/A></TT>

</FONT>
<HR>
<BR>
<CENTER>
DO NOT email links in an unsolicited email! (aka SPAM)!<BR>
Thank you for your support!</CENTER><BR>
<CENTER>[ <A HREF=/account.php>Return to Account Page</A> ]
</TD></TR></TABLE>');
}
else {
	echo("<B>Must Be Logged in to view the rest of this page.</B></TD></TR></TABLE>");
}?>
<?php
include("footer.php");
?>