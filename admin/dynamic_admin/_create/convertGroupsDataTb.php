<?php

/*
	- Converts pre-existing dynamic_propertiesforgroup values into the new dynamic_groupdata
	- Run as a one off at initial setup for upgrades and existing 
	- Run after the groupsdata table has been setu
	
	groupid	jobrole	region
	2		Manager	North
	5		Admin	South
	9		Driver	West

*/

	$dbscript = TRUE;
	require_once(dirname(__FILE__) . '\..\..\..\lib\phpmailer\class.phpmailer.php');
	require_once(dirname(__FILE__) . '\..\..\..\config.php'); //main moodle config
	require_once(dirname(__FILE__) . '\..\config.php'); //plugin config
	
	
	//Creates The User Profile Table. Used to speed up queries. Configure values below and run every night
	function convertGroupDataTable(){
		global $CFG, $DB, $reportAdditionalIds, $reportAdditionalColumns,$mail;
		$error = FALSE;
		//Using normal php/mysql methods here because standard moodle ones don't return errors and no support for drop table
		$con = mysql_connect($CFG->dbhost ,$CFG->dbuser ,$CFG->dbpassword);
		mysql_select_db($CFG->dbname, $con);
		$noOfFields = count($reportAdditionalIds);
		
		$sql = "SELECT * FROM mdl_dynamic_propertiesforgroup";
		$result = mysql_query($sql);
		$insertValues = "";
		if ($result){
			while($row = mysql_fetch_array($result)) {
				//first check row is in table otherwise will error
				$sqlCheck = "SELECT groupid FROM mdl_dynamic_groupdata WHERE groupid = " . $row['group_id'];
				$resultCheck = mysql_query($sqlCheck);
				
				if (mysql_num_rows($resultCheck) > 0){
					//$sqlInsert = "UPDATE mdl_dynamic_groupdata (groupid," . $row['propertyname']   . ") VALUES (" . $row['group_id']  . ",'" .  $row['propertyvalue']    . "')";
					$sqlUpdate = "UPDATE mdl_dynamic_groupdata SET " . $row['propertyname']   . " = '" . $row['propertyvalue'] . "' WHERE groupid =" . $row['group_id'];
					echo $sqlUpdate. "<br>";
					$result2 = mysql_query($sqlUpdate);
					//echo $result2;
				}else{
					$sqlInsert = "INSERT INTO mdl_dynamic_groupdata (groupid," . $row['propertyname']   . ") VALUES (" . $row['group_id']  . ",'" .  $row['propertyvalue']    . "')";
					echo $sqlInsert . "<br>";
					$result2 = mysql_query($sqlInsert);
				}
			}
		}

		mysql_close($con);
		
	}
	//Call function above
	convertGroupDataTable();

?>
