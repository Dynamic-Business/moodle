<?php

/*
	- Converts groupdata table back to propertiesforgroup table
	- Run as a one off at initial setup for upgrades and existing 
	
	groupid	jobrole	region
	2		Manager	North
	5		Admin	South
	9		Driver	West

	Converts to:

	group_id 	field 	data
	2			region 	North
	2			region 	West
	3 			jobrole	Checkout

	- Instructions after installing new plugin version
	1. Ensure mdl_dynamic_propertiesforgroup has been added to the database and is empty. Also ensure that a unique index exists on groupid/field/data
	2. Run this script
	3. 



*/

	$dbscript = TRUE;
	require_once(dirname(__FILE__) . '\..\..\..\lib\phpmailer\class.phpmailer.php');
	require_once(dirname(__FILE__) . '\..\..\..\config.php'); //main moodle config
	require_once(dirname(__FILE__) . '\..\config.php'); //plugin config
	
	//
	function convertGroupDataTable(){
		global $CFG, $DB, $reportAdditionalIds, $reportAdditionalColumns,$mail;
		$error = FALSE;

		//Using normal php/mysql methods here because standard moodle ones don't return errors and no support for drop table
		$con = mysql_connect($CFG->dbhost ,$CFG->dbuser ,$CFG->dbpassword);
		mysql_select_db($CFG->dbname, $con);
		$noOfFields = count($reportAdditionalIds);

		$sql = "SELECT * FROM mdl_dynamic_groupdata";
		$result = mysql_query($sql);
		if ($result){
			while($row = mysql_fetch_assoc($result)) {

				foreach($row as $column=>$value) {
     				 if($value != NULL && $column != 'id' && $column != 'groupid'){
     				 	$sqlInsert = "INSERT IGNORE INTO mdl_dynamic_propertiesforgroup (groupid,field,data) VALUES (" . $row['groupid'] . ",'".$column."','".$value."')";
     				 	//echo $sqlInsert . "<br>";
     				 	$result2 = mysql_query($sqlInsert);
     				 	if($result2){
     				 		echo "INSERT SUCCESS - groupid: ".$row['groupid']." | " . "field: " . $column . " | data: " . $value . "<br>";
     				 	}
     				 }
    			}

			}

		}
		echo " == Complete == ";
		mysql_close($con);
		
	}
	//Call function above
	convertGroupDataTable();

?>
