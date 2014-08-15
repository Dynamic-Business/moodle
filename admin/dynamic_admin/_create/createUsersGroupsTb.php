<?php

	define('CLI_SCRIPT', true);
/*
	Creates the User Groups Table. Will create something like the following:
	
	id	groupid	userid
	1	44		1
	2	44		2
	3	45		1

*/
	set_time_limit(0);
	$dbscript = TRUE;
	require_once(dirname(__FILE__) . '\..\..\..\lib\phpmailer\class.phpmailer.php');
	require_once(dirname(__FILE__) . '\..\..\..\config.php'); //main moodle config
	require_once(dirname(__FILE__) . '\..\config.php'); //plugin config
	// ----- //
	function createUsersGroupsTable(){
		global $CFG, $db, $reportAdditionalIds, $reportAdditionalColumns,$tableReportEmail,$mail;
		$mailMessage = "";
		$error = FALSE;
		$noOfFields = count($reportAdditionalIds);
		
		//Using normal php/mysql methods here because standard moodle ones don't return errors and no support for drop table
		$con = mysql_connect($CFG->dbhost ,$CFG->dbuser ,$CFG->dbpassword);
		mysql_select_db($CFG->dbname, $con);

		$sql = "DROP TABLE IF EXISTS mdl_dynamic_usersgroups";
		if (mysql_query($sql)){
		 	$mailMessage .=  "Table mdl_dynamic_usersgroups deleted successfully (if existed) \n";
		}else{
		  	$mailMessage .= "Error deleting table mdl_dynamic_usersgroups: " . mysql_error() . "\n";
			$error = TRUE;
		}

		$sql = 
			"CREATE TABLE IF NOT EXISTS mdl_dynamic_usersgroups (
				id bigint(10) NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (id),
				groupid bigint(10) NOT NULL,
				userid bigint(10) NOT NULL,
				INDEX (groupid),
				INDEX (userid ASC),
				UNIQUE INDEX (groupid, userid ASC)
			);";
		if (mysql_query($sql)){
		 	$mailMessage .=  "Table mdl_dynamic_usersgroups created successfully \n";
		}else{
		  	$mailMessage .= "Error creating table mdl_dynamic_usersgroups: " . mysql_error() . "\n";
			$error = TRUE;
		}
	
		

		//Get all the group ids of groups with at least one defintion has been set 
		$sql = " 
			SELECT DISTINCT(g.id),dateafter,datebefore FROM mdl_dynamic_group g
			LEFT JOIN mdl_dynamic_propertiesforgroup pg ON g.id = pg.groupid
			WHERE groupid IS NOT NULL ";
		$result = mysql_query($sql);
		while ($row = mysql_fetch_array($result)) {
			//Get all the fields for each groupid 
			$groupid = $row['id'];
			//$academy = $row['academy'];
			$sql = " SELECT DISTINCT(field) FROM mdl_dynamic_propertiesforgroup WHERE groupid = " . $groupid;
			$rs = mysql_query($sql);
			$ins = array();
			while ($row2 = mysql_fetch_array($rs)) {
				//Get all the data for each field for each groupid
				$tmp = array();
				$sql = "SELECT * FROM mdl_dynamic_propertiesforgroup WHERE field = '" . $row2['field'] ."' AND groupid = " . $groupid;
				$rs2 = mysql_query($sql);
				while ($row3 = mysql_fetch_array($rs2)) {
					array_push($tmp, "'" .$row3['data']. "'");
				}
				$ins[$row2['field']] = $tmp;
				//echo  $row['id'] . " " . $row2['field'] . ": " . $ins . "<br>"; //debug
			}
			//Create the combined query

			$sql = "INSERT IGNORE INTO mdl_dynamic_usersgroups (userid,groupid) ";
			$sql .= " SELECT userid," .$groupid. " AS 'groupid' FROM mdl_dynamic_userdata WHERE ";
			$firstrun = TRUE;
			foreach($ins as $key => $value){
				//$i = implode(',',$value);
				$sql .= $firstrun ? "" : " AND ";
				$sql .= $key . " IN (" .  implode(',',$value) . ")";
				$firstrun = FALSE;		
			}
			/*if($academy == 1){
				$sql .= " AND datestarted > 1335830460 ";
			}*/

			if($row['dateafter'] != 0){
				$sql .= " AND datestarted > {$row['dateafter']} ";
			}
			if($row['datebefore'] != 0){
				$sql .= " AND datestarted < {$row['datebefore']} ";
			}

			echo $sql . "<br>"; //debug

			if (mysql_query($sql)){
		 		$mailMessage .=  "Table mdl_dynamic_usersgroups populated successfully with groupid:" . $groupid   . "<br> \n";
			}else{
		  		$mailMessage .= "Error Populating mdl_dynamic_usersgroups with groupid:" . $groupid   . ". Errors: " . mysql_error() . " <br> \n";
				$error = TRUE;
			}

		}

		//Mail Message. Could Eventually just email if an error happens using $error variable
		echo $mailMessage . "<br>";

		//Loop thourgh each group getting all the properties

		//--Delete any Orphaned Properties(?)

	}
	
	//Call function above
	createUsersGroupsTable();	
	

?>
