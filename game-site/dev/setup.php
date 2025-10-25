<?php 
include ("../common.php");

if(file_exists(CFG_FILE_GAME)) {
	header("Location: /");
	exit();
}

$settings = array(
	"Game site config",
	
	get_default_value("SERVER_ID", "game1", "Server ID"),
	get_default_value("GAME_SERVER_EXTERNAL_IP", get_host(), "Game Server External Host"),
	get_default_value("EXCHANGE_RAGE", "125000", "USD -> HI1 Exchange Rate"),
	
	"Game server",
	get_default_value("GAME_SERVER_PROPRETIES", CFG_DIR . "/server.properties", "Path to server.properties")
);

if(sizeof($_POST) > 0) {
	gen_cfg_web(CFG_FILE_GAME, $settings);
	header("Location: /");
	exit();
}

include ("../web/header.php");
?>

<form method="post" action="/dev/setup.php">
<h1> Configuring game1.cfg </h1>
<?php
	for($i = 0; $i < count($settings); $i++) {
		$setting = $settings[$i];
		if(gettype($setting) == "string") {
			echo("<h2>" . $setting . "</h2>");
		}
		else {
			if(!is_set_via_cfg($setting["name"])) {
				echo("<p><b>". $setting["desc"] . "</b> is managed by environment variables.</p>");
			}
			else {
				echo("<p><b>" . $setting["desc"] . "</b>: <input type='".$setting["type"]."' name='".$setting["name"]."' value='".$setting["value"]."'/></p>");
			}
		}
		
	}
	
?>
<input type="submit" value="Confirm & write game1.cfg"/>
</form>

<hr>
<h3> Trans Rights are Human Rights! </h3>
<?php
include ("../web/footer.php")
?>