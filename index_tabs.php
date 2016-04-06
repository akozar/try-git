<html>
<head>
	<!-- Latest compiled and minified CSS -->
	<meta charset="utf-8"/>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script><meta charset="utf-8">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="style.css">
	<link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
	<script src="script.js"></script>
</head>
<body>

	<div id="addDeviceModal" class="modal fade" tabindex="-1" role="dialog">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title">Подтверждение добавления нового станка</h4>
	      </div>
	      <div class="modal-body">
	        <br>
	      </div>
	      <div class="modal-footer">	      	
	        <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
	        <button type="button" class="btn btn-primary">Добавить станок</button>
	      </div>
	    </div>
	  </div>
	</div>

	<div id="deleteItemModal"class="modal fade" tabindex="-1" role="dialog">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> Предупреждение об удалении</h4>
	      </div>
	      <div class="modal-body">
	        <p></p>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
	        <button type="button" class="btn btn-danger">Удалить</button>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->	
	<?php
		header('Content-Type: text/html; charset=utf-8');
		include 'connect.php';
		include 'tablechecker.php';
		// $db_server is a an object that represents the connection to a MySQL Server
		$db_server->query('SET NAMES utf8');
	?>
	<div class="container-fluid">
		<br>
		<?php
			if(!$db_server) die('<div class="alert alert-danger alert-dismissible text-center" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<strong>Unable to connect to MySQL: '.mysqli_connect_errno().'</strong></div>'); 
			$query = "CREATE TABLE IF NOT EXISTS `storage` (
						  `assortment` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
						  `material` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
						  `quantity` decimal(10,0) NOT NULL DEFAULT '0',
						  `critical_value` decimal(10,0) DEFAULT NULL,
						  `unit` tinytext COLLATE utf8_unicode_ci NOT NULL,
						  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
						  `lastWithdraw` decimal(10,0) NOT NULL DEFAULT '0',
						  PRIMARY KEY (`assortment`,`material`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
			$db_server->query($query);

			// WRITING STORAGE TABLE to JSON file
			$sql = "SELECT * FROM storage";
			$res = $db_server->query($sql) or die('<div class="alert alert-danger alert-dismissible text-center" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<strong>Unable to connect to database: '.$mysqli->error.'</strong></div>');	
			
			if ($res -> num_rows!=0){
				$emparr = array();
				while ($row = mysqli_fetch_assoc($res)){
					$emparray[]=$row;
				}
				$fp = fopen('storagedb.json','w');
				fwrite($fp, json_encode($emparray, JSON_UNESCAPED_UNICODE));
				fclose($fp);
			}

		?>
		<div class="alert alert-danger alert-dismissible text-center" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
			<strong>Внимание!</strong> Количество одного или более сортамента критически мало.
		</div>

		<ul id='tabMenu' class="nav nav-pills nav-justified blue">
		  <li role="presentation" class="active"><a href="#"><i class="fa fa-briefcase"></i> Количество материала</a></li>
		  <li role="presentation"><a href="#"><i class="fa fa-credit-card"></i> Заказ материала</a></li>
		  <li role="presentation"><a href="#"><i class="fa fa-money"></i> Продажа станка</a></li>
		  <li role="presentation"><a href="#"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Операции со складом</a></li>
		  <li role="presentation"><a href="#"><span class="glyphicon glyphicon glyphicon-plus" aria-hidden="true"></span> Операции со станками</a></li>
		  <li role="presentation"><a href="#"><i class="fa fa-history"></i> История продаж</a></li>	
		</ul>

		<div id="comp-avail" class="panel panel-default">	
		<!-- MATERIALS_ASSORTMENT TABLE ORDER BY LAST DATE UPDATE-->	
		<?php
			$query = 'SELECT * FROM storage ORDER BY updated DESC';
			$result = $db_server -> query ($query);
			if (!$result OR !mysqli_num_rows($result) OR (mysqli_num_rows($result)==0))
				echo '<h1 class="text-center">Нет данных для показа</h1>';
			else
				{
					echo '<table class="table table-hover table-condensed table-responsive item-table">
				<tr>
					<td><b>Сортамент</b></td>
					<td><b>Материал</b></td> 
					<td><b>Количество</b></td> 
					<td><b>Последнее изменение</b></td>			
					<td><b>Изменение</b></td>				
				</tr>';

					while ($row = mysqli_fetch_assoc($result)) {
						if ($row['quantity']>$row['critical_value'])
							$rowClass = 'success';
						else
							$rowClass = 'danger';
						echo '<tr class='.$rowClass.'><td><a href="#"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a><a><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>'.$row['assortment'].'</td>';
						echo '<td>'.$row['material'].'</td>';
						echo '<td>'.$row['quantity'].' '.$row['unit'].'</td>';
						echo '<td>'.date('H:i d.m.Y',strtotime($row['updated'])).'</td>';
						echo '<td>'.$row['lastWithdraw'].' '.$row['unit'].'</td></tr>';
					};
					echo '</table>';
				}
		?>
			<br>
		</div>
		<div id="order-material" class="panel panel-default">
			<div class="panel-body">
				<form id="addDeviceToOrderList" class="form-horizontal">
					<div class="form-group col-md-12 col-xs-12">
						<label for="item-name" class="col-md-5 control-label text-right">Введите и выберите название станка: </label>
						<div class="col-md-4">
								<input id="item-name" type="text" class="form-control" placeholder="Название станка">
						</div>
					</div>
					<div class="row col-md-12">
						<div class="col-md-offset-5 col-md-4 text-right">

						</div>
					</div>
					<div class="form-group col-md-12 col-xs-12">
						<label for="itemNameQuantity" class="col-md-5 control-label text-right">Количество: </label>
						<div class="col-md-4">
							<div class="input-group">
								<input id="itemNameQuantity" type="text" class="form-control numbers-only" placeholder="Количество">
								<div class="input-group-addon">шт</div>
							</div>
						</div>
					</div>	
							
					<br>					
					<button type="submit" class="btn btn-success center-block"><span class="glyphicon glyphicon-upload" aria-hidden="true"></span> Добавить</button>
				</form>
				<div class="col-xs-12 col-md-12">
				<h3 class="text-center">Материалы для станков:</h3>
					<div class="col-md-offset-5">
						<ul>
						  <li>Трубогиб x 2</li>
						  <li>Фальцепрокатный станок</li>
						  <li>Станок для профнастила</li>
						</ul>
					</div>
				</div>
				<div class="col-xs-12 col-md-12">
				<h3 class="text-center">Добавить определенный материал:</h3>

						<div class="form-group col-md-12 col-xs-12">
							<label for="customAssortment" class="col-md-5 control-label text-right">Введите сортамент: </label>
							<div class="col-md-4">
									<input id="customAssortment" type="text" class="form-control" placeholder="Сортамент">
							</div>
						</div>							
						<div class="form-group col-md-12 col-xs-12">
							<label for="customMaterial" class="col-md-5 control-label text-right">Введите материал: </label>
							<div class="col-md-4">
									<input id="customMaterial" type="text" class="form-control" placeholder="Материал">
							</div>
						</div>						
						<div class="form-group col-md-12 col-xs-12">
							<label for="customQuantity" class="col-md-5 control-label text-right">Количество: </label>
							<div class="col-md-4">
								<div class="input-group">
									<input id="customQuantity" type="text" class="form-control numbers-only" placeholder="Количество">
									<div class="input-group-addon">шт</div>
								</div>
							</div>
						</div>					
					
				</div>				
				<form id="printMaterials" class="form-horizontal">
					<div class="form-group col-md-12 col-xs-12">
						<h3 class="text-center">Выбранные материалы:</h3>
						<div class="row">
						<div class="checkbox col-md-offset-3 col-md-2">
						  <label>
						    <input type="checkbox" value="" checked>
						    Труба 120х60х6 : Сталь 20 <strong>(500 мм.)</strong>
						  </label>
						</div>				  
						<div class="input-group col-md-2">
							<input type="text" class="form-control numbers-only" placeholder="Другое количество">
						</div>
						<div class="input-group col-md-2">
							<input type="text" class="form-control" placeholder="Комментарий">
						</div>
					   </div>

					   <div class="row">
						<div class="checkbox col-md-offset-3 col-md-2">
						  <label>
						    <input type="checkbox" value="" checked>
						    Труба 150х60х6 : Сталь 20 <strong>(2800 мм.)</strong>
						  </label>
						</div>				  
						<div class="input-group col-md-2">
							<input type="text" class="form-control numbers-only" placeholder="Другое количество">
						</div>
						<div class="input-group col-md-2">
							<input type="text" class="form-control" placeholder="Комментарий">
						</div>
						</div>
						<div class="row">
						<div class="checkbox col-md-offset-3 col-md-2">
						  <label>
						    <input type="checkbox" value="" checked>
						    Шестигранник 24 : Сталь 45 <strong>(120 мм.)</strong>
						  </label>
						</div>				  
						<div class="input-group col-md-2">
							<input type="text" class="form-control numbers-only" placeholder="Другое количество">
						</div>
						<div class="input-group col-md-2">
							<input type="text" class="form-control" placeholder="Комментарий">
						</div>
						</div>
										
					</div>
							
					<br>					
					<button type="submit" class="btn btn-info center-block"><span class="glyphicon glyphicon-print" aria-hidden="true"></span> Распечатать</button>
					<i>Для очистки формы обновите страницу</i>
				</form>
			</div>
		</div>
		<div id="item-sell" class="panel panel-default">

		<div class="panel-body">
				<form class="form-horizontal">
					<div class="form-group">
						<label for="item-name" class="col-md-5 control-label text-right">Выберите станок: </label>
						<div class="col-md-4">
							<select id="sellItemPicker" class="form-control">
<?php

	$query = "SELECT * FROM machines_quantity";
	$result = $db_server -> query($query);
	$currentMachine;
	if ($result OR mysqli_num_rows($result) OR (mysqli_num_rows($result)!==0))
		while ($row = mysqli_fetch_assoc($result)) { 
			if (!isset($currentMachine)){
				$currentMachine = $row['machine'];
			};
			echo '<option>'.$row['machine'].'</option>';
	}	else {
		echo '<option></option>';
	};


?>								
							</select>
						</div>
					</div>
					<div class="row col-md-12">
						
						<div class="col-md-offset-5 col-md-4 text-right">
<?php
	if (isset($currentMachine)){
		$query = "SELECT * FROM machines WHERE machine ='".$currentMachine."'";
		$result = $db_server -> query($query);		
	}
	if (isset($result) AND (mysqli_num_rows($result)!==0)){
		echo '<div class="warn-block">';
			while ($row = mysqli_fetch_assoc($result)) { 
				$query1 = "SELECT * FROM storage WHERE (material = '".$row['material']."' AND assortment = '".$row['assortment']."')";
				$currentQuantity = $row['quantity'];
				$res1 = $db_server -> query($query1);
				$row1 = mysqli_fetch_assoc($res1);
				echo '<span><strong>-'.$currentQuantity.' ['.$row1['quantity'].' '.$row1['unit'].'] <small>x</small> '.$row['assortment'].' ['.$row['material'].']</strong></span><br>';
		};	
		echo '</div>';
	};
?>										
							</div>

					</div>
					
					<div class="form-group">
						<label for="item-name" class="col-md-5 control-label text-right">Количество: </label>
						<div class="col-md-4">
							<div class="input-group">
								<input type="text" id="deviceNum" class="form-control numbers-only" placeholder="Количество: ">
								<div class="input-group-addon">шт</div>
							</div>
						</div>
					</div>	
					<div class="row">
						<div class="col-xs-6 text-right">
					<button type="button" class="btn btn-success btn-md">
						<span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> Продать
					</button>
							</div>
							<div class="col-xs-5 text-left">
					<button type="button" class="btn btn-info btn-md">
						<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Создать заявку
					</button>
					</div>
				</div>
				
				</form>
			</div>
		</div>

		<div id="item-change" class="panel panel-default">
			<div class="panel-body">
				<form name="changeMaterial" class="form-horizontal">
					<h3 id="changeMaterialCaption" class="text-center">Добавление либо изменение текущего сортамента</h3>
					<br>
					<div class="form-group">
						<label for="item-name" class="col-md-5 control-label text-right">Раннее используемые сортаменты: </label>
						<div class="col-md-4">
							<select id="assortChooser" class="form-control">								
								<?php							
									$query = 'SELECT DISTINCT assortment FROM storage ORDER BY assortment ASC';
									$result = $db_server -> query($query);
									if ($result OR mysqli_num_rows($result) OR (mysqli_num_rows($result)!==0))
										while ($row = mysqli_fetch_assoc($result)) { 
											echo '<option>'.$row['assortment'].'</option>';
									}									
								?>								
							</select>
						</div>
						<div class="col-md-1 storage-status hidden-s hidden-xs">
							<p class="green"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></p>
						</div>
					</div>	
					<div class="form-group">
						<div class="col-md-offset-5 col-md-4">
							<input id="newAssortment" type="text" class="form-control" placeholder="Либо введите новый сортамент">
						</div>
						<div class="col-md-1 storage-status hidden-s hidden-xs">
							<p class="red"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></p>
						</div>
					</div>									
					<div class="form-group">

						<label for="item-name" class="col-md-5 control-label text-right">Раннее используемые материалы: </label>
						<div class="col-md-4">
							<select id="materialChooser" class="form-control">
								<?php							
									$query = 'SELECT DISTINCT material FROM storage ORDER BY material ASC';
									$result = $db_server -> query($query);
									if ($result OR mysqli_num_rows($result) OR (mysqli_num_rows($result)!==0))
										while ($row = mysqli_fetch_assoc($result)) { 
											echo '<option>'.$row['material'].'</option>';
									}									
								?>		
							</select>
						</div>				
						<div class="col-md-1 storage-status hidden-s hidden-xs">
							<p class="green"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></p>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-offset-5 col-md-4">
							<input id="newMaterial" type="text" class="form-control" placeholder="Либо введите новый материал">
						</div>
						<div class="col-md-1 storage-status hidden-s hidden-xs">
							<p class="red"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></p>
						</div>						
					</div>					
					<div id="editMode" class="hidden">
						<div class="form-group">
							<label for="item-name" class="col-md-5 control-label text-right">Изменить на: </label>
							<div class="col-md-4">
								<div class="input-group">
									<input id="changeQuantity" type="text" class="form-control numbers-only" placeholder="Изменяется с помощью +/-">
									<div class="input-group-addon unitstatus">мм.</div><input class="form-control text-center" id="currentQuantity" type="text" placeholder="Сейчас: 3245" disabled>
								</div>
							</div>
						</div>		
						<div class="form-group">
							<label for="item-name" class="col-md-5 control-label text-right">Установить критическое количество: </label>
							<div class="col-md-4">
								<div class="input-group">
									<input id="changeCriticalValue" type="text" class="form-control numbers-only" placeholder="">
									<div class="input-group-addon unitstatus">мм.</div>
									<input class="form-control text-center" id="currentCriticalValue" type="text" placeholder="Сейчас: 1230" disabled>
								</div>
							</div>
							<p class="row col-md-offset-5 col-md-4 italic"><i>Оставьте поле пустым, если не хотите менять свойство</i></p>
						</div>
					</div>

					<div id="createMode" class="hidden">
						<div class="form-group">
							<label for="item-name" class="col-md-5 control-label text-right">Установить количество: </label>
							<div class="col-md-3">
									<input id="setQuantity" type="text" class="form-control numbers-only" placeholder="Введите число">
							</div>
							<div class="col-md-2">
								<select id="unitChooser" class="form-control">
									<option>мм.</option>
									<option>м.</option>
									<option>шт.</option>
								</select>
							</div>
						</div>		
						<div class="form-group">
							<label for="item-name" class="col-md-5 control-label text-right">Установить критическое количество: </label>
							<div class="col-md-4">
								<input id="setCriticalValue" type="text" class="form-control numbers-only" placeholder="Введите критическое количество">
							</div>
						</div>
						<br>
					</div>
					<div class="row col-md-4"></div>
					<div class="row">
						<div class="col-xs-12">
					<button type="submit" class="btn btn-success center-block"><span class="glyphicon glyphicon-pencil" style="margin-right:10px" aria-hidden="true"></span>  Внести изменения</button>
						</div>
					</div>
				</form>
			</div>
		</div>

		<div id="add-machine" class="panel panel-default">

			<div class="panel-body">
				<form class="form-horizontal">
					<div class="form-group row">
						<h3 class="text-center add-machine-title">Добавление нового типа станка</h3><br>
						<label for="item-name" class="col-md-5 control-label text-right">Введите название станка: </label>
						<div class="col-md-4">
							<div class="ui-widget">
								<input type="text" id="deviceName" class="form-control" placeholder="Название станка">
							</div>
						</div>
					</div>
					<div class="form-group row">
						<label for="sortPicker" class="col-md-5 control-label text-right">Выберите сортамент: </label>
						<div class="col-md-3">
								<select id="sortPicker" class="form-control">
									<?php							
										$query = 'SELECT DISTINCT assortment FROM storage ORDER BY assortment ASC';
										$result = $db_server -> query($query);
										if ($result OR mysqli_num_rows($result) OR (mysqli_num_rows($result)!==0))
											while ($row = mysqli_fetch_assoc($result)) { 
												echo '<option>'.$row['assortment'].'</option>';
										}									
									?>	
								</select>
						</div>
					</div>
					<div class="form-group row">
						<label for="item-name" class="col-md-5 control-label text-right">Либо введите новый сортамент: </label>
						<div class="col-md-4">
							<input type="text" id="addNewSortName" class="form-control" placeholder="Название сортамента">
						</div>
					</div>					
					<div 	class="form-group row">
						<label for="addMaterialPicker" class="col-md-5 control-label text-right">Выберите материал: </label>
						<div class="col-md-3">
								<select id="addMaterialPicker" class="form-control">
									<?php							
										$query = 'SELECT DISTINCT material FROM storage ORDER BY material ASC';
										$result = $db_server -> query($query);
										if ($result OR mysqli_num_rows($result) OR (mysqli_num_rows($result)!==0))
											while ($row = mysqli_fetch_assoc($result)) { 
												echo '<option>'.$row['material'].'</option>';
										}									
									?>	
									</select>
						</div>
					</div>		
					<div class="form-group row">
						<label for="item-name" class="col-md-5 control-label text-right">Либо введите новый материал: </label>
						<div class="col-md-4">
							<input type="text" id="addNewMaterialName" class="form-control" placeholder="Название материала">
						</div>
					</div>					
					<div class="form-group row">
						<label for="item-name" class="col-md-5 control-label text-right">Введите количество материала: </label>
						<div class="col-md-4">
							<div class="input-group">
								<input id="quantity" type="text" class="form-control numbers-only" placeholder="">
								<div id="measure" class="input-group-addon">мм.</div>
								<input class="form-control text-center" id="disabledInput" type="text" placeholder="Сейчас: 1230" disabled>
							</div>
						</div>
					</div>						
					<div class="form-group row">
							<label for="item-name" class="col-md-5 control-label text-right">Введите количество материала: </label>
							<div class="col-md-3">
									<input id="setQuantityDevTab" type="text" class="form-control numbers-only" placeholder="Введите число">
							</div>
							<div class="col-md-2">
								<select id="unitChooserDevTab" class="form-control">
									<option>мм.</option>
									<option>м.</option>
									<option>шт.</option>
								</select>
							</div>
					</div>	
						<div class="row">
							<div class="col-md-offset-4 col-md-5">
								<button id="addDevice" type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Добавить станок</button>
								<button id="materialToListBtn" type="submit" class="btn btn-warning"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Добавить материал к станку</button>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-md-offset-2 col-md-8">
								<div class="panel panel-default hidden" id="materialPanelForDevice">
									  <div class="panel-heading"><h3 class="panel-title pull-left"><strong>Таблица выбранных материалов для станка</strong></h3><button type="button" id="clearMaterialTable" class="btn btn-danger btn-sm pull-right">
  <span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span> Очистить таблицу
</button>
<div class="clearfix"></div></div>
									  <table id="deviceMaterialsTbl" class="table table-striped table-hover">
									  	<tr><th></th><th>Сортамент</th><th>Материал</th><th>Количество</th></tr>
									  </table>
									</div>
							</div>
						</div>						
				</form>

			</div>
		</div>

		<div class="panel panel-default">		

			<table id="orderHistory" class="table table-hover table-condensed table-responsive item-table">
				<tr >
					<td><b>Станок</b></td>
					<td><b>Количество</b></td> 
					<!-- стоимость? -->
					<td><b>Дата продажи</b></td>							
				</tr>
				<tr class="success">
					<td>Вальц листогибочный</td>
					<td>1</td>
					<td>02.07.2014</td>				
				</tr>	
				<tr class="success">
					<td>Трубогиб</td>
					<td>1</td>
					<td>05.12.2014</td>	
				</tr>				
				<tr class="success">
					<td>Трубогиб</td>
					<td>1</td>
					<td>12.08.2014</td>	
				</tr>				
			</table>
		</div>	

	</div>
	<div class="container">
		<div class="row">
			<h5 class="text-danger"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Добавление определенного материала в заказе материала с использованием AutoComplete</h5>
			<h5 class="text-danger"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Предупреждение при удалении материала, если он используется в различных станках (показать список станков)</h5>
			<h5 class="text-danger"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Указание цены при продаже станка?</h5>
			<h5 class="text-warning"><span class="glyphicon glyphicon-hand-right" aria-hidden="true"></span> <strong>Вывод необходимых материалов при выборе станка в меню "Заказ материалов"</strong></h5>
			<h5 class="text-danger"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Изменение количества материалов при продаже станка, а также запись в историю</h5>
			<h5 class="text-danger"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> В графе продажа станка сделать рабочей кнопку количество</h5>
			<h5 class="text-danger"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Вывод документа при заказе материала либо при продаже станка</h5>

		</div>
	</div>
</body>	
</html>