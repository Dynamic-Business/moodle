<?php

/*
	- This only needs to be run once at setup OR when a new field is addded to the config file for reporting.
	- Creates the Group Data Table from Additional Profile fields. 
	- If the table already exists, then it adds any new columns from the config file (whilst retaining the current data)
	- The adding of data to a a group now updates this table directly, propertiesforgroup
	- dynamic_usersgroups now reads this table.
	
	Will create something like the following (without the data - only sets up structure):
	
	groupid	groouptype	role	region
	2		and			Manager	North
	5		and			Admin	South
	9		or			Driver	West

*/

	$dbscript = TRUE;
	require_once(dirname(__FILE__) . '\..\..\..\lib\phpmailer\class.phpmailer.php');
	require_once(dirname(__FILE__) . '\..\..\..\config.php'); //main moodle config
	require_once(dirname(__FILE__) . '\..\config.php'); //plugin config
	
	
	//Creates The User Profile Table. Used to speed up queries. Configure values below and run every night
	function createGroupDataTable(){
		global $CFG, $DB, $reportAdditionalIds, $reportAdditionalColumns,$mail;
		$mailMessage = "";
		$error = FALSE;
		//Using normal php/mysql methods here because standard moodle ones don't return errors and no support for drop table
		$con = mysql_connect($CFG->dbhost ,$CFG->dbuser ,$CFG->dbpassword);
		mysql_select_db($CFG->dbname, $con);
		$noOfFields = count($reportAdditionalIds);
	   //1. First see if table exists and if doesn't, create it
		$sql2 = "CREATE TABLE IF NOT EXISTS mdl_dynamic_groupdata (
					id BIGINT(10) NOT NULL AUTO_INCREMENT,
					groupid bigint(10) NOT NULL,
					PRIMARY KEY (id,groupid),"; 
					
		for ($i=0;$i<$noOfFields;$i++){
			$sql2 .= $reportAdditionalColumns[$i] . " varchar (100), INDEX ("  .$reportAdditionalColumns[$i]   .  ")";
			if ($i < ($noOfFields-1)){
				$sql2 .= ",";
			}
		}						
		$sql2 .= ")";
		//run the query
		//echo $sql2;
		if (mysql_query($sql2)){
		 	$mailMessage .= "Table mdl_dynamic_groupdata created successfully or it already exists \n";
		}else{
		  	$mailMessage .= "Error creating table mdl_dynamic_groupdata: " . mysql_error() . "\n";
			$error = TRUE;
		}
		//Mail Message. Could Eventually just email if an error happens using $error variable
		
		//2. Add any new columns, if the table already existed.
		for ($i=0;$i<$noOfFields;$i++){
			$sql3 = "SELECT " . $reportAdditionalColumns[$i] .  "  FROM mdl_dynamic_groupdata";
			mysql_query($sql3);
			if (mysql_errno()){
				//if error, i.e. the column doesn't exist, add the column
				$sql3 = "ALTER TABLE mdl_dynamic_groupdata ADD COLUMN " . $reportAdditionalColumns[$i] . " VARCHAR(100) NULL DEFAULT NULL ";
				if (mysql_query($sql3)){
					$mailMessage .= "New Column added successfully \n";
				}else{
					$mailMessage .= "Error creating table mdl_dynamic_groupdata: " . mysql_error() . "\n";
					$error = TRUE;
				}
			}else{
				//else do nothing
			}
		}
		//3. Could potentially add to this so it deletes any columns that aren't in $reportAdditionalColumns[$i] (except groupid column)
		//- $sql4 = SHOW COLUMNS FROM mdl_dynamic_groupdata 
		//- if 'Field' not in $reportAdditionalColumns[$i]
		//- ALTER TABLE mdl_dynamic_groupdata DROP columnthatnolongerexists;
		
		// 
		echo $mailMessage . "<br>";
		
		//Email me if there's an error
		if($error){
			error_log($mailMessage);
			$mail->Subject = 'ERROR! Database [' . $CFG->dbname  . '] Table [mdl_dynamic_groupsdata] Report';
			$mail->Body = $mailMessage ;
			if($mail->Send()){
				echo 'Mail Success!' . "<br />\n";  
			}else{
				echo "Error sending: " . $mail->ErrorInfo;;
			}
		}
		mysql_close($con);
		
	}
	//Call function above
	createGroupDataTable();

?>
