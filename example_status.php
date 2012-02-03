<?php
error_reporting(E_ALL & !E_USER_NOTICE);
ini_set("display_errors", 1);

require_once("MCStatus.php");

if (isset($_GET['host'])) {
	$port = isset($_GET['port']) ? (int)$_GET['port'] : MCStatus::$DEFAULT_PORT;
	$mcs = new MCStatus($_GET['host'], $port);
	echo "<h1>Basic Info</h1>";
	$stuff = $mcs->getBasic();
	echo "<pre>".var_export($stuff,true)."</pre>\n";

	echo "<h1>Full Info</h1>";
	$stuff = $mcs->getFull();
	echo "<pre>".var_export($stuff,true)."</pre>\n";
} else {
	echo "<h1>Usage</h1>";
	echo "<pre>".$_SERVER['SCRIPT_NAME']."?host={hostname}[&port={port}]</pre>\n";
}

?>