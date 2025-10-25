<?php 
include ("../common.php");
include ("../web/header.php");

if(file_exists(CFG_FILE)) {
	header("Location: /setupservers.php");
	exit();
}

$settings = array(
	"Database Credentials",
	
	get_default_value("DB_NAME", "web", "Database Name"),
	get_default_value("DB_IP", "localhost", "Database Host"),
	get_default_value("DB_USERNAME", "horseisle", "Database Username"),
	get_default_value("DB_PASSWORD", "test123", "Database Password"),
	
	"E-Mail Config",
	get_default_value("EMAIL_ACTIVATION", "false", "Email Activation Required", "checkbox"),
	get_default_value("FROM_EMAIL_ADDR", "support@horseisle.com", "Email \"From\" Address"),

	"Subscriptions",
	get_default_value("PP_URI", "/web/ppemu.php", "PayPal Emulator location"),
	
	"General",
	get_default_value("MAIN_DOMAIN", "//".get_host(), "Main Site External Domain")
);

if(sizeof($_POST) > 0) {
	gen_cfg_web(CFG_FILE, $settings);
	header("Location: /setupservers.php");
	exit();
}


?>

<form method="post" action="/dev/setup.php">
<h1> Configuring web.cfg </h1>
<?php
	for($i = 0; $i < count($settings); $i++) {
		$setting = $settings[$i];
		if(gettype($setting) == "string") {
			echo("<h2>" . $setting . "</h2>");
		}
		else {
			if(!is_set_via_cfg($setting["name"])) {
				echo("<p>". $setting["desc"] . " is managed by environment variables.</p>");
			}
			else {
				echo("<p><b>" . $setting["desc"] . "</b>: <input type='".$setting["type"]."' name='".$setting["name"]."' value='".$setting["value"]."'/></p>");
			}
		}
		
	}
	
?>
<input type="submit" value="Write web.cfg"/>
</form>

<hr>
<h3> Trans Rights are Human Rights! </h3>
<?php
include ("../web/footer.php")
?>