<?php
	include 'connect.php';
	// $db_server is a an object that represents the connection to a MySQL Server
	$db_server->query('SET NAMES utf8');
	if (isset($_POST['formName'])){
		echo 'Form '.$_POST['formName'].' is submitting.';
		// {formName:formName, editMode: editMode, assortment: assortment, material: material, quantity: quantity, criticalValue: criticalValue, unit: unit}
		if ($_POST['formName']=='changeMaterial'){
			echo ' Edit mode is '.$_POST['editMode'].'.';			
			if ($_POST['editMode']=='true'){
				// Updating storage
				$query = "SELECT quantity, critical_value FROM storage WHERE assortment='".$_POST["assortment"]."' AND material = '". $_POST["material"]."';";
				$result = $db_server -> query($query);
				$row = mysqli_fetch_assoc($result);				
				$quantity = floatval($row['quantity']) + floatval($_POST['quantity']);
				if ($_POST['criticalValue']!=""){
					$criticalValueChangeQuery = ', critical_value = '.floatval($_POST['criticalValue']);
				} else {
					$criticalValueChangeQuery = '';
				}
				$query = "UPDATE storage SET quantity = ".$quantity.$criticalValueChangeQuery." WHERE assortment ='".$_POST["assortment"]."' AND material = '". $_POST["material"]."';";
				$result = $db_server -> query($query);
			} else {
				// Inserting new items
				$query = "INSERT INTO storage SET assortment='".$_POST["assortment"]."',material='". $_POST["material"]."',quantity=".$_POST["quantity"].",critical_value=".$_POST['criticalValue'].",unit='".$_POST['unit']."';";
				$result = $db_server -> query($query);
			}
		}

	};

?>