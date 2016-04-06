<?php
	include 'connect.php';
	// $db_server is a an object that represents the connection to a MySQL Server
	$db_server->query('SET NAMES utf8');

	if (isset($_POST['deviceName'])){
		$query = "SELECT * FROM machines WHERE machine =".$_POST['deviceName']."";
		$result = $db_server -> query($query);	
	}

	if ($result AND (mysqli_num_rows($result)!==0)){
		$a = array();
		$i = 0;
			while ($row = mysqli_fetch_assoc($result)) { 
				$query1 = "SELECT * FROM storage WHERE (material = '".$row['material']."' AND assortment = '".$row['assortment']."')";
				$currentQuantity = $row['quantity'];

				$a[$i]['assort']=$row['assortment'];				
				$a[$i]['material']=$row['material'];			
				$a[$i]['quantity']=$row['quantity'];			
				$a[$i]['assort']=$row['assortment'];		


				$res1 = $db_server -> query($query1);
				$row1 = mysqli_fetch_assoc($res1);
				echo '<span><strong>-'.$currentQuantity.' ['.$row1['quantity'].' '.$row1['unit'].'] <small>x</small> '.$row['assortment'].' ['.$row['material'].']</strong></span><br>';
				$i++;
		};	
	};

?>