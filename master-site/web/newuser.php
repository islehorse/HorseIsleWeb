<?php
include('../config.php');
include('../common.php');
include("header.php");
$atype = 2;
if(isset($_GET["A"]))
	$atype = $_GET["A"];
if($atype > 2 || $atype < 1)
	$atype = 2;

$problems = [];

if(isset( $_GET["U"], $_GET["AC"] )){
	$verify_username = $_GET["U"];
	$verify_token = $_GET["AC"];
	
	$hmac = GenHmacMessage($verify_username, "UserActivation", false);
	$hmac_hash = bin2hex(base64_url_decode($verify_token));
	
	if(strlen($hmac_hash) != 64){
		print_r(strlen($hmac_hash));
		echo("<B>INCOMPLETE Activation Code!</B><BR>");
		include("footer.php");
		exit();		
	}
	else{
		print("<BR> Attempting to Activate your account...<BR>");
		if(hash_equals($hmac_hash, $hmac)) {
			if(user_exists($verify_username)) {
				$user_id = get_userid($verify_username);
				if(!get_email_activation_status($user_id)) {
					$connect = mysqli_connect($dbhost, $dbuser, $dbpass,$dbname) or die("Unable to connect to '$dbhost'");
					$stmt = $connect->prepare("UPDATE Users SET EmailActivated='YES' WHERE Id=?"); 
					$stmt->bind_param("i", $user_id);
					$stmt->execute();
					echo(' <B><FONT COLOR=GREEN>COMPLETED: Successfully Enabled your Account.</B>  You may Log in with your name and password at the upper right.</FONT><BR><BR>    <!-- Google Code for signup Conversion Page -->
    <script language="JavaScript" type="text/javascript">
    <!--
    var google_conversion_id = 1059728575;
    var google_conversion_language = "en_US";
    var google_conversion_format = "2";
    var google_conversion_color = "EDE5B4";
    if (1) {
      var google_conversion_value = 1;
    }
    var google_conversion_label = "signup";
    //-->
    </script>
    <script language="JavaScript" src="http://www.googleadservices.com/pagead/conversion.js">
    </script>
    <noscript>
    <img height=1 width=1 border=0 src="http://www.googleadservices.com/pagead/conversion/1059728575/imp.gif?value=1&label=signup&script=0">
    </noscript>

    ');
					include("footer.php");
					exit();

				}
				else{
					echo("<B><FONT COLOR=RED>ACCOUNT ALREADY ACTIVATED:</B> Your account has already been activated. Please login with your username and password.</B></FONT><BR><BR><TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=100%>");
					include("footer.php");
					exit();
				}
			}
		}
		echo("<B><FONT COLOR=RED>FAILED:</B> Invalid User/Code Combination. After 50 hours unactivated accounts are removed from the system. So if it's been over 2 days,  you will have to try signing up again.</B></FONT><BR><BR>");
		include("footer.php");
		exit();
	}

}
else if(isset( $_POST['user'],$_POST['pass1'],$_POST['pass2'],$_POST['sex'],$_POST['email'],$_POST['age'],$_POST['passreqq'],$_POST['passreqa'] ,$_POST['A']))
{
	if(isset($_POST["cbr"]))
	{
		if($_POST['cbr'] !== "OK")
			array_push($problems, "You need to read the RULES and agree to follow them!");
	}
	else
	{
		array_push($problems, "You need to read the RULES and agree to follow them!");
	}
	if(isset($_POST["cbt"]))
	{
		if($_POST['cbt'] !== "OK")
			array_push($problems, "You need to read the Terms and Conditions agree to be bound by them!");
	}
	else
	{
		array_push($problems, "You need to read the Terms and Conditions agree to be bound by them!");
	}
	if($_POST['A'] == 1){
		if(isset($_POST["cbp"]))
		{
			if($_POST['cbp'] !== "OK")
				array_push($problems, "You need to have Parental Permission!");
		}
		else
		{
			array_push($problems, "You need to have Parental Permission!");
		}
	}
	if($_POST['pass1'] !== $_POST['pass2'])
		array_push($problems, "Passwords must match!");
	
	$username = $_POST['user'];
	$password = $_POST['pass1'];
	$age = intval($_POST['age'],10);
	$email = $_POST['email'];
	$reset_question = $_POST['passreqq'];
	$reset_answer = $_POST['passreqa'];
	$country = $_POST['country'];
	$gender = $_POST['sex'];
	
	if(preg_match('/[^A-Za-z]/', $username))
		array_push($problems, "Username must contain ONLY Letters.");
	
	$username_len = strlen($username);
	if($username_len < 3)
		array_push($problems, "Username must be at least 3 characters long.");
	if($username_len > 16)
		array_push($problems, "Username must be less than 16 characters long.");
	
	if(preg_match('/[A-Z]{2,}/',$username))
		array_push($problems, "Username should be formatted with the first letter of each word capitalized. ( For example: BlueBunny )");
	
	if(strtoupper($username)[0] !== $username[0])
		array_push($problems, "Username should be formatted with the first letter of each word capitalized. ( For example: BlueBunny )");
	
	if(preg_match('/[^A-Za-z0-9]/',$password))
		array_push($problems, "Password must contain ONLY Letters and numbers.");
	$password_len = strlen($password);
	if($password_len < 6)
		array_push($problems, "Password must be at least 6 characters long.");

	if($password_len > 16)
		array_push($problems, "Password must be less than 16 characters long.");
	
	if(!preg_match('/[0-9]/',$password))
		array_push($problems, "Password must contain at least one number.");
	
	if(!preg_match('/[a-zA-Z]/',$password))
		array_push($problems, "Password must contain at least one letter.");
	
	if($reset_question == "Select a question")
		array_push($problems, "You must select a Password Recovery Question.");
	if($reset_answer == "")
		array_push($problems, "You must Answer the Password Recovery Question.");
		
	if($country == "")
		array_push($problems, "Please enter your country.");
	
	if($_POST['age'] == "")
		array_push($problems, "Please enter your age.");
		
	if($username == $password)
		array_push($problems, "Username and Password can not be the same!");
	
	if(strpos($username, $password) !== false)
		array_push($problems, "The password cannot be within the username!.");
	
	if(strpos($password, $username) !== false)
		array_push($problems, "The password cannot have the username within it!.");
	
	
	if(!preg_match('/^[A-Za-z0-9_.+-]*\@[A-Za-z0-9_.+-]*\.[A-Za-z0-9_.+-]{1,4}$/',$email))
		array_push($problems, "Email does not appear valid, you will not be able sign in without getting the login mail.");
	
	
	populate_db();
	$connect = mysqli_connect($dbhost, $dbuser, $dbpass,$dbname) or die("Unable to connect to '$dbhost'");
	$result = mysqli_query($connect, "SELECT MAX(Id) FROM Users");
	$user_id = $result->fetch_row()[0] + 1;
	if($user_id == NULL)
		$user_id = 0;
		
	$salt = random_bytes ( 64 );
	$answer_hash = hash_salt($reset_answer,$salt);
	$password_hash = hash_salt($password,$salt);
	$hex_salt = bin2hex($salt);


	if(user_exists($username))
		array_push($problems, "Username taken. Please try a different account name.");


	if(count($problems) <= 0)
	{
		$activated = "NO";
		if(!$email_activation){
			$activated = "YES";
		}

		$stmt = $connect->prepare("INSERT INTO Users VALUES(?,?,?,?,?,?,?,?,?,?,'NO','NO',?)"); 
		$stmt->bind_param("isssssissss", $user_id, $username, $email, $country, $reset_question, $answer_hash, $age, $password_hash, $hex_salt, $gender, $activated);
		$stmt->execute();
		
		send_activation_email($email, $username, $password);
		
		echo('<TABLE cellpadding=10><TR><TD><B>Your account has been added!</B><BR>Look for the email from '.$from_email.' with your activation code!<BR>You cannot play until you CLICK the link with your code in the email.<BR>  Be sure to check your Spam email box in case it goes there. If you do not get the email soon, feel free to log in with your username and password to re-send the Activation Code to the same or a different email address.<BR><BR><A HREF=/>Go Back to Main Page</A><BR><BR></TD></TR></TABLE>');
		include("footer.php");
		exit();
	}
}

function generate_name(){
	$dict = file_get_contents("dictonary.dic");
	$words = explode("\r\n", $dict);
	
	$name = "";
	while(true){
		$word = $words[array_rand($words)];
		if(strlen($name.$word) > 16)
			break;
		
		$name .= $word;
		
		if(strlen($name) > 5)
			if(rand(0, 100) <= 15)
				break;
	}
	
	if(user_exists($name))
		$name = generate_name();
	
	return $name;
}
?>
<CENTER><TABLE WIDTH=90% BORDER=0><TR><TD VALIGN=top>

<FONT SIZE=+2><B>Horse Isle Create New Account:</B></FONT><BR>
<I>Only one account per person.  Make sure you have parental permission if under 13!</I><BR>
<BR>
<FORM METHOD=POST>
<?php
if(count($problems) > 0)
{
	echo("<B>There were the following problems with your submission:<BR><FONT COLOR=RED>");
	for($i = 0; $i < count($problems); $i++)
	{
		echo($problems[$i]."<BR>");
	}
	echo("</FONT></B>");
}
?>
<B>GAME DETAILS (Take time selecting a good username, it will be your game name):</B><BR>
<FONT COLOR=005500>In order to make the game prettier, please capitalize the first letter of each word in Username:<BR>
<FONT SIZE=+1 COLOR=502070>Good:<FONT COLOR=GREEN>BlueBunny</FONT>  Not:<FONT COLOR=RED>BLUEBUNNY</FONT> or <FONT COLOR=RED>bluebunny</FONT> or <FONT COLOR=RED>BlUebuNNy</FONT></FONT><BR>
If the username you choose is offensive in anyway, your account will be deleted as soon as it's noticed. 
Please do not use any part of your real name in the username.  Pick something fun and original.  There are some ideas on right.<BR></FONT>
Desired username: <INPUT TYPE=TEXT SIZE=16 MAX=16 VALUE="<?php if(isset($_POST["user"])){echo(htmlspecialchars($_POST["user"],ENT_QUOTES));};?>" NAME="user"><I><FONT SIZE-1>[3-16 letters only, capitalize first letter of each word ]</FONT></I><BR>
Desired password: <INPUT TYPE=PASSWORD SIZE=16 MAX=16 VALUE="<?php if(isset($_POST["pass1"])){echo(htmlspecialchars($_POST["pass1"],ENT_QUOTES));};?>" NAME="pass1"><I><FONT SIZE-1>[6-16 both letters and numbers only, case insensitive]</FONT></I><BR>
Repeat&nbsp; password: <INPUT TYPE=PASSWORD SIZE=16 MAX=16 VALUE="<?php if(isset($_POST["pass2"])){echo(htmlspecialchars($_POST["pass2"],ENT_QUOTES));};?>" NAME="pass2"><I><FONT SIZE-1>[ same as above ]</FONT></I><BR>


GIRL: <INPUT TYPE=RADIO SIZE=30 NAME="sex" VALUE="FEMALE" <?php if(isset($_POST["sex"])){if($_POST["sex"] == "FEMALE"){echo("CHECKED");}}else{echo("CHECKED");}?>>
 BOY: <INPUT TYPE=RADIO SIZE=30 NAME="sex" VALUE="MALE" <?php if(isset($_POST["sex"])){if($_POST["sex"] == "MALE"){echo("CHECKED");}};?>> <I>[Determines whether you are referred to as 'him' or 'her' in game.]</I>
<BR>


<BR>
<B>PERSONAL DETAILS (Kept private,  never shared):</B><BR>
<?php
$email = "";
if(isset($_POST["email"])){
	$email = htmlspecialchars($_POST["email"],ENT_QUOTES);
};

if($atype == 2)
	echo("Your Valid Email: <INPUT TYPE=TEXT SIZE=40 NAME=email VALUE='".$email."'><I><FONT SIZE-1>[ Login codes sent here ]</FONT></I><BR><FONT SIZE=-1 COLOR=880000>* many mail programs will mistakingly identify the Email as Spam, you may have to check your spam folders.  If the email code is not received within 2 days(50hrs), the account is removed, and you will then have to add it again.</FONT><BR>");
else if($atype == 1)
	echo("Your <B>PARENT'S</B> Email: <INPUT TYPE=TEXT SIZE=40 NAME=email VALUE='".$email."'><I><FONT SIZE-1>[ Login codes sent here ]</FONT></I><BR><FONT SIZE=-1 COLOR=880000>* many mail programs will mistakingly identify the Email as Spam, you may have to check your spam folders.  If the email code is not received within 2 days(50hrs), the account is removed, and you will then have to add it again.</FONT><BR>");
?>
Your Age: <INPUT TYPE=TEXT SIZE=4 NAME="age" VALUE="<?php if(isset($_POST["age"])){echo(htmlspecialchars($_POST["age"],ENT_QUOTES));};?>">
Your Country: <INPUT TYPE=TEXT SIZE=30 NAME="country" VALUE="<?php if(isset($_POST["country"])){echo(htmlspecialchars($_POST["country"],ENT_QUOTES));};?>"><BR>
Password Recovery Question:<SELECT NAME=passreqq>
<OPTION><?php 
if(isset($_POST["passreqq"])){echo(htmlspecialchars($_POST["passreqq"],ENT_QUOTES));}else{echo("Select a question");}
?>
<OPTION>My favorite food
<OPTION>My pets name
<OPTION>My best friends first name
<OPTION>My favorite singer
<OPTION>My favorite sports star
<OPTION>My favorite team
<OPTION>My favorite cartoon character
<OPTION>My favorite actor
</SELECT> Answer:<INPUT TYPE=TEXT SIZE=15 NAME=passreqa VALUE='<?php if(isset($_POST["passreqa"])){echo(htmlspecialchars($_POST["passreqa"],ENT_QUOTES));};?>'><BR><BR>
<B>LEGALITIES (Only Check if TRUE!):</B><BR>
I have Read and Understand and will follow the <A HREF=rules.php>Rules</A>: <INPUT TYPE=CHECKBOX NAME="cbr" VALUE="OK" <?php if(isset($_POST["cbr"])){if($_POST["cbr"] == "OK"){echo("CHECKED");}};?>><BR>
I have Read and Understand the <A HREF=termsandconditions.php>Terms and Conditions</A>: <INPUT TYPE=CHECKBOX NAME="cbt" VALUE="OK" <?php if(isset($_POST["cbt"])){if($_POST["cbt"] == "OK"){echo("CHECKED");}};?>><BR>
<?php
echo('<INPUT TYPE=HIDDEN NAME=A VALUE='.$atype.'>');
if($atype == 1){
	$msg = "";
	if(isset($_POST["cbp"]))
		if($_POST["cbp"] == "OK")
			$msg = "CHECKED";
	echo('By clicking this I <B>PROMISE</B> I have parental permission: <INPUT TYPE=CHECKBOX NAME=cbp VALUE=OK '.$msg.'><BR>');
}
?>
<BR>
<INPUT TYPE=SUBMIT VALUE='CREATE NEW ACCOUNT'><BR>
</FORM>
<BR>
<A HREF=/>Go Back to Main Page</A><BR><BR>

</TD><TD>
<TABLE BGCOLOR=FFEEEE BORDER=1 CELLPADDING=4><TR BGCOLOR=EEDDEE><TD COLSPAN=2><CENTER>
<B>Some Random Available Names:</B><BR>(pick one or make up your own)<BR>
</TD></TR><TR><TD><CENTER><FONT SIZE=-1>
<?php for($i = 0; $i < 30; $i++) { echo(htmlspecialchars(generate_name()).'<BR>'); }?></FONT></TD><TD><FONT SIZE=-1><CENTER><?php for($i = 0; $i < 30; $i++) { echo(htmlspecialchars(generate_name()).'<BR>'); }?></FONT></TD></TR></TABLE>
</TD></TR></TABLE>
<?php
include("footer.php");
?>
