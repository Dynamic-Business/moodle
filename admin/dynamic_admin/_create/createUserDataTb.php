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
	function create_user_data_table(){
		global $CFG, $DB, $reportAdditionalIds, $reportAdditionalColumns,$mail;
		$mailMessage = "";
		$error = FALSE;
		
		if(!isset($reportAdditionalIds)){
			echo "&#10008; Report columns have not been configured in Server > Dynamic Plugins therefore this table has not been created.";
			return;
		}
		/*echo "<pre>";
		var_dump($CFG);
		die;*/
		
		//Using normal php/mysql methods here because standard moodle ones don't return errors and no support for drop table
		$con = mysqli_connect($CFG->dbhost ,$CFG->dbuser ,$CFG->dbpassword);
		mysqli_select_db($con, $CFG->dbname);

		mysqli_set_charset($con,'utf8');
		mysqli_query($con,"set names 'utf8'"); 

		// New in 7.5 - Needed for Heinz (but beneficial for all) so single or double quotes are stripped out of all data before creating table ---------
		// May need to change drop down fields so that they pickup userdata table and not user_info_data otherwise may be slight inconsistency.
		// Also this has not been speed tested on mass data - if problems arise then it may be due to this.
		$sqlUpdate1 = 'UPDATE mdl_user_info_data SET DATA = REPLACE(data,\'"\',\'\')';
		$sqlUpdate2 = "UPDATE mdl_user_info_data SET DATA = REPLACE(data,\"'\",\"\")";
		if (mysqli_query($con,$sqlUpdate1)){
			$mailMessage .= "&#10004; Replaced Quotes 1<br>";
		}else{
			$mailMessage .= "&#10008; Replaced Quotes 1 Fail<br>";
		}
		if (mysqli_query($con,$sqlUpdate2)){
			$mailMessage .= "&#10004; Replaced Quotes 2<br>";
		}else{
			$mailMessage .= "&#10008; Replaced Quotes 2 Fail<br>";
		}
		//---------------------------------------------------------------------------------------------------------------------------------------------
		
		
		$sql1 = "DROP TABLE IF EXISTS mdl_dynamic_userdata";
		if (mysqli_query($con,$sql1)){
		 	$mailMessage .=  "&#10004; Table mdl_dynamic_userdata deleted successfully (if existed)<br> \n";
		}else{
		  	$mailMessage .= "&#10008; deleting table mdl_dynamic_userdata: " . mysqli_error($con) . "\n";
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
		for ($i=0;$i<$noOfFields;$i++){

			$sql2 .= " LEFT JOIN mdl_user_info_data AS " . $reportAdditionalColumns[$i] . " ON u.id = " . $reportAdditionalColumns[$i] . ".userid AND ." . $reportAdditionalColumns[$i] .".fieldid=" . $reportAdditionalIds[$i] ." ";

		}
		$sql2 .= ")";
		
		//run the query
		if (mysqli_query($con,$sql2)){
		 	$mailMessage .= "&#10004; Table mdl_dynamic_userdata created successfully<br> \n";
		}else{
		  	$mailMessage .= "&#10008; creating table mdl_dynamic_userdata: " . mysqli_error($con) . "\n";
			$error = TRUE;
		}
		
		//Mail Message. Could Eventually just email if an error happens using $error variable
		echo $mailMessage . "<br>";
		
		//Email me if there's an error
		if($error){
			error_log($mailMessage);
			$mail->Subject = '&#10008; Database [' . $CFG->dbname  . '] Table [mdl_dynamic_usersdata] Report';
			$mail->Body = $mailMessage ;
			if($mail->Send()){
				echo 'Mail Success!' . "<br />\n";  
			}else{
				echo "Error sending: " . $mail->ErrorInfo;;
			}
		}
		
	}
	
	//Call function above
	create_user_data_table();	
	
	
	

?>
