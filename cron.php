<?php
require_once(dirname(__file__)."/"."../connection.php");
mysql_select_db("admin_host",$mysqlcon);

// Hämta data
$r = mysql_query("SELECT * FROM vent");
$info = array();
while ($row = mysql_fetch_assoc($r)) {
	$info[] = $row;
}

// find all expire
foreach($info AS $v) {
	if (strtotime($v['timeout'])<strtotime("now")) {

		exec("ps -Af | grep ventrilo", $output);

		foreach($output AS $k) {
			if (strstr($k, 'ventrilo/'.$v['port'])) {
				$s = explode(" ", preg_replace('/(\s+)/', ' ',$k));
				$process = $s[1];
				break;
			}
		}
		exec("kill ". $process);
		mysql_query("DELETE FROM vent WHERE id =" . $v['id']);
		//echo $process;
	}
	
	if ($v['online']==0) {
		// Starta
		$path = "/home/admin/domains/zencodez.net/ventrilo/";
		exec("/var/ventrilo21/ventrilo_srv -f" . $path . $v['port'] . " -d");
		
		mysql_query("UPDATE vent SET online = 1 WHERE id =" . $v['id']);
	}
}
?>