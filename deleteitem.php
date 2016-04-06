<?php
	include 'connect.php';
	// $db_server is a an object that represents the connection to a MySQL Server
	$db_server->query('SET NAMES utf8');
	$query = "DELETE FROM storage WHERE assortment='".$_POST["assortment"]."' AND material = '". $_POST["material"]."';";
	$db_server -> query($query);
?>	
