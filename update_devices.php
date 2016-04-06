<?php
	include 'connect.php';
	// $db_server is a an object that represents the connection to a MySQL Server
	$db_server->query('SET NAMES utf8');

	if (isset($_POST['device'])){
		$obj = json_decode($_POST['device'], true);
		$machine_name = $obj["deviceName"];

		// IF MACHINE EXISTS IT WILL DELETE ALL ROWS THAT WERE EXIST BEFORE
		$query = "DELETE FROM machines WHERE machine =".$machine_name;
		$db_server -> query($query);
		/*			var currentElement  = {
						assort: assort,
						material: material,
						quantity: quantity,
						unit: unit						
					};		*/
		for ($i = 0; $i < count($obj["contains"]); $i++)
		{
			// Inserting elements of device. One request per iteration.
			$assort = $obj["contains"][$i]['assort'];
			$material = $obj["contains"][$i]['material'];
			$quantity = $obj["contains"][$i]['quantity'];
			$unit = $obj["contains"][$i]['unit'];
			
			echo "Inserting element ".$i." of machine ".$machine_name." (assortment: ".$assort.", material: ".$material.", quantity: ".$quantity.", unit: ",$unit,")";
			// INSERT INTO machines
			$query = "REPLACE INTO machines VALUES (DEFAULT, '".$machine_name."','".$material."','".$assort."','".$quantity."')";

			$db_server -> query($query);

			// INSERT new values of assort and material and unit INTO storage 
			$queryInsert = "INSERT INTO storage VALUES ('" . $assort . "', '" . $material . "', DEFAULT, DEFAULT, '" . $unit . "', DEFAULT, DEFAULT)";
			$db_server -> query($queryInsert);		
		};
		// UPDATE 'machines_quantity' table

		$queryInsert = "INSERT INTO machines_quantity VALUES ('" . $machine_name . "', 0)";
		$db_server -> query($queryInsert);	
	};

?>