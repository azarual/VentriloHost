<?php
 
/*
------------------------------------------------------------------------------
RECEIVE_SMS.PHP
------------------------------------------------------------------------------
 
Detta script kan ta emot SMS-meddelanden från MO-SMS.
Allt som ekas ut av ditt skript kommer att hamna i ett svars-SMS till
slutanvändaren. Det är alltså mycket viktigt att skriptet inte genererar några
felmeddelanden eller liknande. Om inget svar ekas ut inom 30 sekunder skickar
MO-SMS automatiskt ut ett "Tack för din beställning"-SMS av debiterings-
tekniska skäl.
 
SMS:et levereras i teckenkodning ISO 8859-1
 
------------------------------------------------------------------------------
*/
 
// Stäng av PHP:s felrapportering
error_reporting(0);

// Kontrollera att det är MO-SMS som anropar (OBS: kommentera bort vid testkörning!) 
if($_SERVER['REMOTE_ADDR'] != '94.247.169.159') {   
	exit();   
} 
 
// Plocka ut avsändarnumret
$nr = $_REQUEST['nr'];
 
// Plocka ut SMS-meddelandet
$sms = urldecode($_REQUEST['sms']);
 
// Plocka ut priset slutanvändaren blev debiterad (för egen vinststatistik)
$tariff = $_REQUEST['tariff'];
 
// Plocka ut operatören SMS:et skickades in via (för egen vinststatistik)
$operator = $_REQUEST['operator'];
 
// Eka ut svaret som skickas tillbaka till slutanvändaren
//echo 'Vi har nu tagit emot ditt SMS!';
 
// ==========================================================
require_once("../connection.php");
mysql_select_db("admin_host",$mysqlcon);
// Hämta data
$r = mysql_query("SELECT * FROM vent");
$info = array();
while ($row = mysql_fetch_assoc($r)) {
	$info[] = $row;
}

// Kolla om mobilen finns om den finns förläng, annars skapa
$match = false;
foreach($info AS $v) {
	if ($v['mobil'] == $nr) {
		$match = true;
		break;
	}
}

// Tid
if ($tariff == 10) {
	$tid = "1 WEEK";
} else {
	$tid = "1 WEEK";
}

// Förläng
if ($match) {
	mysql_query("UPDATE vent SET timeout = DATE_ADD(timeout, INTERVAL " . $tid . ") WHERE mobil = '" . $nr . "' ");
	$r = mysql_query("SELECT timeout FROM vent WHERE mobil = '" . $nr . "' ");
	echo "Servertiden har förlängts till " . mysql_result($r,0);
} else {
// Skapa
	// Max 10 st server
	if (count($info)<30) {
		$port = rand(3701, 3770);
		$free=false;
		while ($free==false) {
			$free = true;
			$port = rand(3701, 3770);
			foreach($info AS $v) {
				if ($v['port'] == $port) {
					$free = false;
				}
			}
		}
		// DB
		mysql_query("INSERT INTO vent (mobil, port, timeout) VALUES ('" . $nr . "', " . $port . ", DATE_ADD(NOW( ), INTERVAL " . $tid . ")) ");
		// PW
		$adminpw = substr(md5("vent".microtime()),0,4);
		
		$path = "/home/admin/domains/zencodez.net/ventrilo/";
		// Kopiera
		copy($path . "ventrilo_srv.usr", $path . $port . ".usr");
		// Kopiera och ändra config filen
		copy($path . "ventrilo_srv.ini", $path . $port . ".ini");

		file_put_contents($path . $port . ".ini", str_replace(array('AdminPassword=','Port=3784'), array("AdminPassword=" . $adminpw,'Port='.$port), file_get_contents($path . "ventrilo_srv.ini")));
		
		$r = mysql_query("SELECT timeout FROM vent WHERE mobil = '" . $nr . "' ");
		echo "Servern startas inom 1 minut. IP: zencodez.net PORT: " . $port . " ADMINPW: " . $adminpw;
		echo " Servern är giltig till: " . mysql_result($r,0);
		echo " SMS:a igen om du vill förlänga servertiden.";
	} else {
		// Meddela
		echo "Alla server är tyvärr upptagna.";
	}
}

?>