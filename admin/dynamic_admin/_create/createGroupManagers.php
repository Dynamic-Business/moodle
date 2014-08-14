<?php

/*
	- used for NEXT to automatically enrol and assign Group Managers based on specific fields
	- works on the assumption that a group has been set up with definition properties for each store
	- Are Group Managers defined ONLY by this script? If so then Manually assigned GM's are redundant - REMOVE!

*/
	define('CLI_SCRIPT', true);
	
	$gmRoleId = 99; //Group Manager
	$gmcRoleId = 100; //Group Manager
	$contextId = 1; //top level
	$fieldForGroupSetup = "storedetails";
	$dbscript = TRUE;

	require_once(dirname(__FILE__) . '\..\..\..\lib\phpmailer\class.phpmailer.php');
	require_once(dirname(__FILE__) . '\..\..\..\config.php'); //main moodle config
	require_once(dirname(__FILE__) . '\..\config.php'); //plugin config

	function createGroupManagers(){
		global $CFG, $DB, $reportAdditionalIds, $reportAdditionalColumns,$mail,$fieldForGroupSetup,$gmRoleId,$gmcRoleId,$contextId ;
		$mailMessage = "";
		$error = FALSE;
		$con = mysql_connect($CFG->dbhost ,$CFG->dbuser ,$CFG->dbpassword);
		mysql_select_db($CFG->dbname, $con);

		// 1. Delete all group manager assignments from roles table and dynamic_managers_group
		//$sqldelete = "DELETE FROM mdl_role_assignments WHERE roleid = " . $gmRoleId . " AND component = 'dynamic-auto' ";
		$sqldelete = "DELETE FROM mdl_role_assignments WHERE roleid IN (" . $gmRoleId . "," . $gmcRoleId . ") AND component = 'dynamic-auto' ";

		$result = mysql_query($sqldelete);
		if($result){
			echo "- " . mysql_affected_rows() . " users removed as Group Manager/coaches role.<br>";
		}

		$sqldelete2 = "DELETE FROM mdl_dynamic_managers_group WHERE autocreated = 1 ";
		// $sqldelete = "DELETE FROM mdl_role_assignments WHERE roleid = " . $gmRoleId;
		$result = mysql_query($sqldelete2);
		if($result){
			echo "- " . mysql_affected_rows() . " users unassigned from dynamic_managers_group<br><br>";
		}

		//=======================================================================================================
		// 2. Get the Managers and their store and the group to be assigned to (group already setup)
		$sql = "
			SELECT 
				userid,
				ud." . $fieldForGroupSetup . ",
				groupid 
			FROM mdl_dynamic_userdata ud
			INNER JOIN (SELECT groupid,data AS " . $fieldForGroupSetup . " FROM mdl_dynamic_propertiesforgroup WHERE field='storedetails') dg ON ud." . $fieldForGroupSetup . " = dg." . $fieldForGroupSetup . "
			INNER JOIN mdl_dynamic_group g ON g.id = dg.groupid 
			WHERE (ud.jobcode IN (
			'MDEP',
			'MGRA',
			'MGRB',
			'MGRC',
			'MGRS',
			'MNDM',
			'MOFF',
			'MOPA',
			'MOWM',
			'MPTM',
			'MPTO',
			'MSAM',
			'MSEC',
			'MSTK',
			'MCSA',
			'MCDE',
			'MCOF',
			'SECO') 
			OR (ud.jobcode = 'SCON' AND ud.jobgrade = 'SNR'))
			AND g.name REGEXP '[0-9][0-9][0-9][0-9]$'
		";

		$sqlcoaches = "
			SELECT 
				userid,
				ud." . $fieldForGroupSetup . ",
				groupid 
			FROM mdl_dynamic_userdata ud
			INNER JOIN (SELECT groupid,data AS " . $fieldForGroupSetup . " FROM mdl_dynamic_propertiesforgroup WHERE field='storedetails') dg ON ud." . $fieldForGroupSetup . " = dg." . $fieldForGroupSetup . " 
			INNER JOIN mdl_dynamic_group g ON g.id = dg.groupid 
			WHERE ((ud.teamcoach = 1) OR (ud.headteamcoach = 1))
			AND g.name REGEXP '[0-9][0-9][0-9][0-9]$'

		";
		// echo "<pre>";
		// echo $sql; 
		// echo $sqlcoaches; die;

		//=========================================================================================================

		$result = mysql_query($sql);
		if ($result){
			$userCount = 0;
			$userCount2 = 0;
			while($row = mysql_fetch_array($result)) {

				//Add the user as a Group Manager within mdl_role_assignments
				$sqlInsert = "
					INSERT IGNORE INTO mdl_role_assignments (roleid,contextid,userid,timemodified,modifierid,component) VALUES  (" . $gmRoleId . "," . $contextId . "," . $row['userid'] . ",UNIX_TIMESTAMP(NOW()),2,'dynamic-auto')
				";
				//echo $sql1 . "<br>";
				$result1 = mysql_query($sqlInsert);
				if($result1){
					$sql2 = "
						INSERT IGNORE INTO mdl_dynamic_managers_group (userid,groupid,autocreated) VALUES (" . $row['userid'] . "," . $row['groupid'] . ",1)
					";
					if(!mysql_query($sql2)){
						$mailMessage .= "Error Running Query: " . mysql_error() . "\n";
						$error = TRUE;
					}else{
						$userCount2 ++;
					}
				}else{
					$mailMessage .= "Error Running Query: " . mysql_error() . "\n";
					$error = TRUE;
				}
				$userCount ++;
			}
			echo "- " . $userCount . " users assigned as Group Manager role.<br>";
			echo "- " . $userCount2 . " users assigned as Group Managers to group.<br>";
		}else{
			$mailMessage .= "Error Running Query: " . mysql_error() . "\n";
			$error = TRUE;
		}

		if($error){
			echo $mailMessage;
			error_log("Group Manager Auto Script Error: " . $mailMessage);
		}
		//

		// echo $sqlcoaches;
		// echo "<br><br>";
		$result = mysql_query($sqlcoaches);
		if ($result){
			$userCount = 0;
			$userCount2 = 0;
			while($row = mysql_fetch_array($result)) {

				//Add the user as a Group Manager within mdl_role_assignments
				$sqlInsert = "
					INSERT IGNORE INTO mdl_role_assignments (roleid,contextid,userid,timemodified,modifierid,component) VALUES  (" . $gmcRoleId . "," . $contextId . "," . $row['userid'] . ",UNIX_TIMESTAMP(NOW()),2,'dynamic-auto')
				";
				//echo $sql1 . "<br>";
				$result1 = mysql_query($sqlInsert);
				if($result1){
					$sql2 = "
						INSERT IGNORE INTO mdl_dynamic_managers_group (userid,groupid,autocreated) VALUES (" . $row['userid'] . "," . $row['groupid'] . ",1)
					";
					if(!mysql_query($sql2)){
						$mailMessage .= "Error Running Query: " . mysql_error() . "\n";
						$error = TRUE;
					}else{
						$userCount2 ++;
					}
				}else{
					$mailMessage .= "Error Running Query: " . mysql_error() . "\n";
					$error = TRUE;
				}
				$userCount ++;
			}
			echo "- " . $userCount . " users assigned as Group Manager Coaches role.<br>";
			echo "- " . $userCount2 . " users assigned as Group Managers Coaches to group.<br>";
		}else{
			$mailMessage .= "Error Running Query: " . mysql_error() . "\n";
			$error = TRUE;
		}

		if($error){
			echo $mailMessage;
			error_log("Group Manager Coaches Auto Script Error: " . $mailMessage);
		}

	}
	
	createGroupManagers();

?>
