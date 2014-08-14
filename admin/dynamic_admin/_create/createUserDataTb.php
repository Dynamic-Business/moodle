<?php

	define('CLI_SCRIPT', true);
/*
	Creates the User Data Table from Additional Profile fields. Will create something like the following:
	
	userid	role	region
	1		Manager	North
	2		Admin	South
	3		Driver	West

*/

	$dbscript = TRUE;
	require_once(dirname(__FILE__) . '\..\..\..\lib\phpmailer\class.phpmailer.php');
	require_once(dirname(__FILE__) . '\..\..\..\config.php'); //main moodle config
	require_once(dirname(__FILE__) . '\..\config.php'); //plugin config


	
	
	//Creates The User Profile Table. Used to speed up queries. Configure values below and run every night
	function createUserDataTable(){
		global $CFG, $DB, $reportAdditionalIds, $reportAdditionalColumns,$mail;
		$mailMessage = "";
		$error = FALSE;
		
		
		
		//Using normal php/mysql methods here because standard moodle ones don't return errors and no support for drop table
		$con = mysql_connect($CFG->dbhost ,$CFG->dbuser ,$CFG->dbpassword);
		mysql_select_db($CFG->dbname, $con);
		//Don't forget to close further down
		
		//New in 7.5 - Needed for Heinz (but beneficial for all) so single or double quotes are stripped out of all data before creating table ---------
		//May need to change drop down fields so that they pickup userdata table and not user_info_data otherwise may be slight inconsistency.
		//Also this has not been speed tested on mass data - if problems arise then it may be due to this.
		$sqlUpdate1 = 'UPDATE mdl_user_info_data SET DATA = REPLACE(data,\'"\',\'\')';
		$sqlUpdate2 = "UPDATE mdl_user_info_data SET DATA = REPLACE(data,\"'\",\"\")";
		if (mysql_query($sqlUpdate1)){
			$mailMessage .= "Replaced Quotes 1 Success<br>";
		}else{
			$mailMessage .= "Replaced Quotes 1 Fail<br>";
		}
		if (mysql_query($sqlUpdate2)){
			$mailMessage .= "Replaced Quotes 2 Success<br>";
		}else{
			$mailMessage .= "Replaced Quotes 2 Fail<br>";
		}
		//---------------------------------------------------------------------------------------------------------------------------------------------
		
		
		
		$sql1 = "DROP TABLE IF EXISTS mdl_dynamic_userdata";
		if (mysql_query($sql1)){
		 	$mailMessage .=  "Table mdl_dynamic_userdata deleted successfully (if existed) \n";
		}else{
		  	$mailMessage .= "Error deleting table mdl_dynamic_userdata: " . mysql_error() . "\n";
			$error = TRUE;
		}
		
		//---------------------------------------
		
		$sql2 = "CREATE TABLE  mdl_dynamic_userdata (
					userid bigint(10) NOT NULL,
					PRIMARY KEY (userid),"; 
		
		$noOfFields = count($reportAdditionalIds);
		
		for ($i=0;$i<$noOfFields;$i++){
		
			$sql2 .= $reportAdditionalColumns[$i] . " varchar (100), INDEX ("  .$reportAdditionalColumns[$i]   .  ")";
			if ($i < ($noOfFields-1)){
				$sql2 .= ",";
			}
		}	
							
		$sql2 .= ") (SELECT 
						u.id AS 'userid',";
		
						
		for ($i=0;$i<$noOfFields;$i++){
			$sql2 .= $reportAdditionalColumns[$i] . ".data AS '" . $reportAdditionalColumns[$i]   . "' ";
			if ($i < ($noOfFields-1)){
				$sql2 .= ",";
			}
		}				

										
		$sql2 .=	"FROM mdl_user u ";

		//===========================================================================================================================================

		// 1. Original Query
		
		/*for ($i=0;$i<$noOfFields;$i++){
			$sql2 .= " LEFT JOIN ( 
							SELECT userid,data 
							FROM mdl_user_info_data 
							WHERE fieldid=" . $reportAdditionalIds[$i] . 
						") AS " . $reportAdditionalColumns[$i] . " ON u.id = " . $reportAdditionalColumns[$i] . ".userid ";
		}
		$tmp = FALSE;*/

		// --- end of original

		//2. Improved query
		for ($i=0;$i<$noOfFields;$i++){
			/*$sql2 .= " LEFT JOIN ( 
							SELECT userid,data 
							FROM mdl_user_info_data 
							WHERE fieldid=" . $reportAdditionalIds[$i] . 
						") AS " . $reportAdditionalColumns[$i] . " ON u.id = " . $reportAdditionalColumns[$i] . ".userid ";*/
			$sql2 .= " INNER JOIN mdl_user_info_data AS " . $reportAdditionalColumns[$i] . " ON u.id = " . $reportAdditionalColumns[$i] . ".userid ";

		}
		$tmp = FALSE;
		for ($i=0;$i<$noOfFields;$i++){
			($tmp == TRUE ? $sql2 .= ' AND '  :  $sql2 .= ' WHERE '); // returns true
			$sql2 .= " " . $reportAdditionalColumns[$i] . ".fieldid=" . $reportAdditionalIds[$i] . " ";
			$tmp = TRUE;
		}

		// --- end of improved query

		//===========================================================================================================================================

		$sql2 .= ")";
		//echo "<br><br>";
		//echo $sql2;
		//echo "<br><br>";
		//run the query
		if (mysql_query($sql2)){
		 	$mailMessage .= "Table mdl_dynamic_userdata created successfully \n";
		}else{
		  	$mailMessage .= "Error creating table mdl_dynamic_userdata: " . mysql_error() . "\n";
			$error = TRUE;
		}
		
		//Mail Message. Could Eventually just email if an error happens using $error variable
		echo $mailMessage . "<br>";
		
		//Email me if there's an error
		if($error){
			error_log($mailMessage);
			$mail->Subject = 'ERROR! Database [' . $CFG->dbname  . '] Table [mdl_dynamic_usersdata] Report';
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
	createUserDataTable();	
	
	
	

?>
