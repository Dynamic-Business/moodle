<?php
	define('CLI_SCRIPT', true);
/*

	New script to create table that replcaes the 'View' for External Database Enrolments.

*/

	$dbscript = TRUE;
	require_once(dirname(__FILE__) . '\..\..\..\lib\phpmailer\class.phpmailer.php');
	require_once(dirname(__FILE__) . '\..\..\..\config.php'); //main moodle config
	require_once(dirname(__FILE__) . '\..\config.php'); //plugin config
	
	function createExDbEnrolTable(){
		global $CFG, $db,$tableReportEmail,$mail;
		$mailMessage = "";
		$error = FALSE;
		//$noOfFields = count($reportAdditionalIds);
		
		//Using normal php/mysql methods here because standard moodle ones don't return errors and no support for drop table
		$con = mysql_connect($CFG->dbhost ,$CFG->dbuser ,$CFG->dbpassword);
		mysql_select_db($CFG->dbname, $con);
		//
		
		$sql1 = "DROP TABLE IF EXISTS mdl_dynamic_exdbenrol";
		if (mysql_query($sql1)){
		 	$mailMessage .=  "Table mdl_dynamic_exdbenrol deleted successfully (if existed) \n";
		}else{
		  	$mailMessage .= "Error deleting table mdl_dynamic_exdbenrol: " . mysql_error() . "\n";
			$error = TRUE;
		}
		
		//NEW --------------------------------
			$sql2 = "CREATE TABLE mdl_dynamic_exdbenrol (
						id bigint(10) NOT NULL AUTO_INCREMENT,
						PRIMARY KEY (id),
						user_id varchar(20) NOT NULL,
						course_id varchar(20) NOT NULL,
						INDEX (user_id),
						INDEX (course_id ASC),
						UNIQUE INDEX (user_id, course_id ASC)
					)
					SELECT 
						u.idnumber AS 'user_id',
						cg.course_id AS 'course_id'
					FROM mdl_dynamic_usersgroups ug 
					INNER JOIN mdl_dynamic_courses_groups cg ON cg.group_id = ug.groupid
					INNER JOIN mdl_user u ON u.id = ug.userid
					WHERE (u.idnumber <> '') AND (u.deleted <> 1)
					GROUP BY u.idnumber,cg.course_id
					
					";

				//--------------------------------------
		//echo $sql2;
		if (mysql_query($sql2)){
		 	$mailMessage .=  "Table mdl_dynamic_exdbenrol created successfully \n";
		}else{
		  	$mailMessage .= "Error creating table mdl_dynamic_exdbenrol: " . mysql_error() . "\n";
			$error = TRUE;
		}
		//echo $sql2;
		
		//Mail Message. Could Eventually just email if an error happens using $error variable
		echo $mailMessage . "<br>";
		
		//Email me if there's an error
		if($error){
			error_log($mailMessage);
			$mail->Subject = 'ERROR! Database [' . $CFG->dbname  . '] Table [mdl_dynamic_exdbenrol] Report';
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
	createExDbEnrolTable();	
	
	
	

?>
