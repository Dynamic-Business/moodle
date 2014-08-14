<?php

/*
	- Used for Heinz to convert AD information to mdl_user_info_data. The AD plugin cannot 
	populate to user profile fields, therefore this scripts takes existing mdl_user fields,
	and populates mdl_user_info_data.
	
	- Edit the config fields below and set up as a scheduled task 


*/
	/* config - change to whatever fields will be populated */
	$dataFields = array("institution","department");						
	$dataFieldIds = array(1,2);
	//
	require_once(dirname(__FILE__) . '\..\..\..\config.php'); //main moodle config
	require_once(dirname(__FILE__) . '\..\config.php'); //plugin config

	function addProfileData(){
		global $CFG, $DB, $dataFields, $dataFieldIds,$mail;
		$mailMessage = "";
		$error = FALSE;
		
		//Using normal php/mysql methods here because standard moodle ones don't return errors and no support for drop table
		$con = mysql_connect($CFG->dbhost ,$CFG->dbuser ,$CFG->dbpassword);
		mysql_select_db($CFG->dbname, $con);
		
		//Drop the table to begin with
		//$sql1 = "DROP TABLE IF EXISTS mdl_user_info_data";
		$sql1 = "DELETE FROM mdl_user_info_data";
		if (mysql_query($sql1)){
		 	$mailMessage .=  "Table mdl_user_info_data data deleted successfully (if existed) \n";
		}else{
		  	$mailMessage .= "Error deleting data from table mdl_user_info_data: " . mysql_error() . "\n";
			$error = TRUE;
		}
		
		//reset auto_increment back to 1
		$sql1b = "ALTER TABLE mdl_user_info_data AUTO_INCREMENT = 1 ";
		if (mysql_query($sql1b)){
		 	$mailMessage .=  "auto increment changed successfully (if existed) \n";
		}else{
		  	$mailMessage .= "Error changing auto increment on mdl_user_info_data: " . mysql_error() . "\n";
			$error = TRUE;
		}
		
		//INSERT QUERY
		$sql3 .= " INSERT INTO mdl_user_info_data (fieldid,userid,data) ";
		$noOfFields = count($dataFields);				
		for ($i=0;$i<$noOfFields;$i++){	
			$sql3 .= "SELECT " . $dataFieldIds[$i] . " AS 'fieldid', id AS 'userid', " . $dataFields[$i] . " AS 'data' FROM mdl_user WHERE " . $dataFields[$i] . " != '' ";
			if ($i < ($noOfFields-1)){
				$sql3 .= " UNION ";
			}	
		}
		//echo "<br><br>sql3:" . $sql3;
		
		if (mysql_query($sql3)){
		 	$mailMessage .= "Table populating mdl_user_info_data successfully \n";
		}else{
		  	$mailMessage .= "Error populating mdl_user_info_data: " . mysql_error() . "\n";
			$error = TRUE;
		}
		
		//Mail Message. Could Eventually just email if an error happens using $error variable
		echo $mailMessage . "<br>";
		
		//Email me if there's an error
		if($error){
			$mail->Subject = 'ERROR! Database [' . $CFG->dbname  . '] Table [mdl_dynamic_usersdata] Report';
			$mail->Body = $mailMessage ;
			if($mail->Send()){
				echo 'Mail Success!' . "<br />\n";  
			}else{
				echo "Error sending: " . $mail->ErrorInfo;
			}
		}
		
		mysql_close($con);
		
	}
	
	//Call function above
	addProfileData();
	

?>