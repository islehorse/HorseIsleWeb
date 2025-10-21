<?php
if(!function_exists('is_logged_in'))
	include('../common.php');
$cfg = get_cfg();
?>
<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=100%>
<TR>
<TD><IMG SRC=/web/hoilgui10.gif></TD>
<TD WIDTH=100% BACKGROUND=/web/hoilgui11.gif></TD>
<TD><IMG SRC=/web/hoilgui12.gif></TD>
</TR></TABLE>
<CENTER><B>
[ <A HREF=http:<?php echo($cfg["MAIN_DOMAIN"]); ?>/web/rules.php>Rules</A> ]
[ <A HREF=http:<?php echo($cfg["MAIN_DOMAIN"]); ?>/web/termsandconditions.php>Terms and Conditions</A> ]
[ <A HREF=http:<?php echo($cfg["MAIN_DOMAIN"]); ?>/web/privacypolicy.php>Privacy Policy</A> ]</B><BR>
[ <A HREF=http:<?php echo($cfg["MAIN_DOMAIN"]); ?>/web/expectedbehavior.php>Expected Behavior</A> ]
[ <A HREF=http:<?php echo($cfg["MAIN_DOMAIN"]); ?>/web/contactus.php>Contact Us</A> ] 
[ <A HREF=http:<?php echo($cfg["MAIN_DOMAIN"]); ?>/web/credits.php>Credits</A> ]<BR>
<FONT FACE=Verdana,Arial SIZE=-2>Copyright &copy; <?php echo(date("Y")); ?> Horse Isle</FONT>

<!-- Google Analytics -->
<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
</script>
<script type="text/javascript">
_uacct = "UA-1805076-1";
urchinTracker();
</script>

