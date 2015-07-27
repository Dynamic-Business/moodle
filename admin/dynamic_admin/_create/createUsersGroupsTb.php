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
		global $CFG, $DB, $reportAdditionalIds, $reportAdditionalColumns,$mail,$dategroups;
		$mailMessage = "";
		$error = FALSE;
		$noOfFields = count($reportAdditionalIds);
		
		//Using normal php/mysql methods here because standard moodle ones don't return errors and no support for drop table
		$con = mysqli_connect($CFG->dbhost ,$CFG->dbuser ,$CFG->dbpassword);
		mysqli_select_db($con,$CFG->dbname);
		mysqli_set_charset($con,'utf8');
		mysqli_query($con,"set names 'utf8'");

		$sql = "DROP TABLE IF EXISTS mdl_dynamic_usersgroups";
		if (mysqli_query($con,$sql)){
		 	$mailMessage .=  "&#10004; Table mdl_dynamic_usersgroups deleted successfully (if existed)<br> \n";
		}else{
		  	$mailMessage .= "&#10008; deleting table mdl_dynamic_usersgroups: " . mysqli_error($con) . "\n";
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
		if (mysqli_query($con,$sql)){
		 	$mailMessage .=  "&#10004; Table mdl_dynamic_usersgroups created successfully<br> \n";
		}else{
		  	$mailMessage .= "&#10008; creating table mdl_dynamic_usersgroups: " . mysqli_error($con) . "\n";
			$error = TRUE;
		}
		

		//Get all the group ids of groups with at least one defintion has been set 
		$sql = " 
			SELECT DISTINCT(g.id),dateafter,datebefore FROM mdl_dynamic_group g
			LEFT JOIN mdl_dynamic_propertiesforgroup pg ON g.id = pg.groupid
			WHERE groupid IS NOT NULL ";
		$result = mysqli_query($con,$sql);
		if($result){
			while ($row = mysqli_fetch_array($result)) {
				//Get all the fields for each groupid 
				$groupid = $row['id'];
				$sql = " SELECT DISTINCT(field) FROM mdl_dynamic_propertiesforgroup WHERE groupid = " . $groupid;
				$rs = mysqli_query($con,$sql);
				$ins = array();
				while ($row2 = mysqli_fetch_array($rs)) {
					//Get all the data for each field for each groupid
					$tmp = array();
					$sql = "SELECT * FROM mdl_dynamic_propertiesforgroup WHERE field = '" . $row2['field'] ."' AND groupid = " . $groupid;
					$rs2 = mysqli_query($con,$sql);
					while ($row3 = mysqli_fetch_array($rs2)) {
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

				if($row['dateafter'] != 0){
					$sql .= " AND datestarted > {$row['dateafter']} ";
				}
				if($row['datebefore'] != 0){
					$sql .= " AND datestarted < {$row['datebefore']} ";
				}

				echo $sql . "<br>"; //debug

				if (mysqli_query($con,$sql)){
			 		$mailMessage .=  "&#10004; Table mdl_dynamic_usersgroups populated successfully with groupid:" . $groupid   . "<br> \n";
				}else{
			  		$mailMessage .= "&#10008; Populating mdl_dynamic_usersgroups with groupid:" . $groupid   . ". Errors: " . mysqli_error($con) . " <br> \n";
					$error = TRUE;
				}
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
