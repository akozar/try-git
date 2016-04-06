<?php
	include "connect.php";
	$db_server->query('SET NAMES utf8');
	$machine = $_POST['deviceName'];
	$query = "SELECT * FROM machines WHERE machine = '".$machine."'";
	$res = $db_server -> query ($query);
	if ($res -> num_rows != 0) {
		$i = 0;
		while ($row = $res -> fetch_assoc()){
			$unitQuery = "SELECT quantity,unit FROM storage WHERE material = '".$row['material']."' AND assortment = '".$row['assortment']."'";
			$unitRes = $db_server -> query ($unitQuery);
			$unitRow = $unitRes -> fetch_assoc();
			$data[$i][] = $row['machine'];
			$data[$i][] = $row['material'];
			$data[$i][] = $row['assortment'];
			$data[$i][] = $row['quantity'];
			$data[$i][] = $unitRow['unit'];
			$data[$i][] = $unitRow['quantity'];
			$i++;
		}
	} else {
		$data = false;
	}

	echo json_encode($data,JSON_UNESCAPED_UNICODE);
?>