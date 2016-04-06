<?php
	include "connect.php";

	function isTableExist ( $table, $db_handler ){
		if ( mysqli_num_rows( $db_handler -> query( "SHOW TABLES LIKE '".$table."'" ) ) == 1 ) 
	    	return true;
		else 
			return false;		
	}

	$tableNames = ['machines', 'machines_quantity', 'storage'];
	$tableCreateQueries = ["
		CREATE TABLE IF NOT EXISTS `machines` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `machine` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
		  `material` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
		  `assortment` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
		  `quantity` decimal(10,0) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;
	",
	"
		CREATE TABLE IF NOT EXISTS `machines_quantity` (
		  `machine` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		  `img` varchar(400) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
		  `quantity` decimal(10,0) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`machine`,`quantity`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;
	",
	"
		CREATE TABLE IF NOT EXISTS `storage` (
		  `assortment` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
		  `material` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
		  `quantity` decimal(10,0) NOT NULL DEFAULT '0',
		  `critical_value` decimal(10,0) DEFAULT NULL,
		  `unit` tinytext COLLATE utf8_unicode_ci NOT NULL,
		  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `lastWithdraw` decimal(10,0) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`assortment`,`material`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
	"];

	for ( $i = 0; $i < count ($tableNames); $i++ ){
		if (!isTableExist( $tableNames [$i] , $db_server)) {
			echo 'Table '.$tableNames[$i].' does not not exist. Creating it...'; 
			$db_server -> query ( $tableCreateQueries[$i] ) or die('Error at creating '.$tableNames[$i].' table: '.$mysqli->error);	
		} 
	};
?>