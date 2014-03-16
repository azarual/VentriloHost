<?php
if (isset($_POST)) {
	$pw = $_POST['pw'];
	$port = $_POST['port'];
	if ($pw == "optifree" && $port == 3786) {
	
		// Starta
		$path = "/var/ventrilo21/";
		echo exec("/var/ventrilo21/ventrilo_srv -f" . $path . intval($port) . " -d");
		echo "Server zencodez.net:" . $port . " startad.";
	}
}
?>
<!DOCTYPE html>
<meta charset="utf-8" />
<title>Ventrilo Host</title>
<form method="post">
	<label for="pw">Lösenord</label>
	<input type="password" name="pw" id="pw" />
	
	<label for="port">Port</label>
	<input type="number" name="port" id="port" />
	
	<input type="submit" value="starta" />
</form>