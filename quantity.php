<?php
	include 'connect.php';
	// $db_server is a an object that represents the connection to a MySQL Server
	$db_server->query('SET NAMES utf8');
	$query = "SELECT `quantity`,`critical_value`,`unit` FROM `storage` WHERE `assortment` = '".$_POST['assortment']."' AND `material` = '".$_POST['material']."';";
	$result = $db_server -> query($query);
	$unit='мм.';
	$quantity=0;
	$criticalValue=0;
	$exist = false;
	if (mysqli_num_rows($result)!==0){
		$row = mysqli_fetch_assoc($result);
		$quantity = $row['quantity'];
		$criticalValue = $row['critical_value'];
		$unit=$row['unit'];
		$exist = true;
	};
	echo json_encode(array("exist"=>$exist,"quantity"=>$quantity,"criticalValue"=>$criticalValue,"unit"=>$unit),JSON_UNESCAPED_UNICODE);
?>