<?php 
include ("../common.php");

if(file_exists(SRV_FILE)) {
	header("Location: /");
	exit();
}

$servers = array(
	array( 
		"id" => "game1", 
		"icon" => "beta.gif",
		"internal_site" => "http://gameweb:80/",
		"external_site" => "http://game1.".get_host()."/",
		"desc" => " ",
	),
);

if(sizeof($_POST) > 0) {
	$servers = array();
	
	for($i = 0; ; $i++) {
		if(isset($_POST[strval($i). "_id"])){
			array_push($servers, array(
				"id" => $_POST[strval($i). "_id"],
				"icon" => $_POST[strval($i). "_icon"],
				"internal_site" => $_POST[strval($i). "_internal_site"],
				"external_site" => $_POST[strval($i). "_external_site"],
				"desc" => $_POST[strval($i). "_desc"]
			));
		}
		else {
			break;
		}
	}
	
	gen_servers(SRV_FILE, $servers);
	header("Location: /");
	exit();
}

include ("../web/header.php");
?>
<script>
	window.server_count = 0;
	function add_server() {
		var doc = document.getElementById("server_list");
		var serv = document.getElementById("0_server").outerHTML;
		
		window.server_count++;
		doc.innerHTML += serv.replaceAll("0_", window.server_count + "_").replaceAll("#0", "#"+window.server_count);
		
	}
	function remove_server() {
		if(window.server_count > 0) {
			var doc = document.getElementById(window.server_count+"_server")
			window.server_count--;
			doc.remove();
		}
		else {
			alert("Cannot remove last server");
		}
	}
</script>
<form method="post" action="/dev/setupservers.php">
<h1> Configuring servers.json </h1>
<div id="server_list">
<?php
	for($i = 0; $i < count($servers); $i++) {
		$server = $servers[$i];
		echo("<div id='".strval($i)."_server' >");
		echo("<h2> Server #".strval($i)."</h2>");
		echo("<p><b>Id: </b><input type='text' name='".strval($i)."_id' value='".$server["id"]."'/></p>");
		echo("<p><b>Icon: </b><input type='text' name='".strval($i)."_icon' value='".$server["icon"]."'/></p>");
		echo("<p><b>Internal Hostname: </b><input type='text' name='".strval($i)."_internal_site' value='".$server["internal_site"]."'/></p>");
		echo("<p><b>External Hostname: </b><input type='text' name='".strval($i)."_external_site' value='".$server["external_site"]."'/></p>");
		echo("<p><b>Server Description: </b><input type='text' name='".strval($i)."_desc' value='".$server["desc"]."'/></p>");
		echo("</div>");
	}
	
?>
</div>
<input type="submit" value="Confirm & write servers.json"/>
</form>
<button onclick="add_server()">Add server</button>
<button onclick="remove_server()">Remove server</button>

<hr>
<h3> Trans Rights are Human Rights! </h3>
<?php
include ("../web/footer.php")
?>