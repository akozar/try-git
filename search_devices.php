<?php
	include "connect.php";
	$db_server->query('SET NAMES utf8');

	$searchTerm = $_GET['term'];
	$query = "SELECT DISTINCT machine FROM machines WHERE machine LIKE '%".$searchTerm."%' ORDER BY machine ASC";
	$res = $db_server -> query ($query);
	while ($row = $res -> fetch_assoc()){
		$data[] = $row['machine'];
	}

	echo json_encode($data);
?>