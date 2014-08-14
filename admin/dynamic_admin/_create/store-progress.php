<?php
	define('CLI_SCRIPT', true);
/*

	Script to create table for Store Progress Report

*/

	$dbscript = TRUE;
	require_once(dirname(__FILE__) . '\..\..\..\lib\phpmailer\class.phpmailer.php');
	require_once(dirname(__FILE__) . '\..\..\..\config.php'); //main moodle config
	require_once(dirname(__FILE__) . '\..\config.php'); //plugin config
	
	function createStoreProgressData(){
		global $CFG, $db,$tableReportEmail,$mail;
		$mailMessage = "";
		$error = FALSE;
		//$noOfFields = count($reportAdditionalIds);
		
		//Using normal php/mysql methods here because standard moodle ones don't return errors and no support for drop table
		$con = mysql_connect($CFG->dbhost ,$CFG->dbuser ,$CFG->dbpassword);
		mysql_select_db($CFG->dbname, $con);
		//
		
		//Create Table for Data
		$sql = "DROP TABLE IF EXISTS mdl_dynamic_spdata";
		$success  = mysql_query($sql) or die(mysql_error());
		$sql = "
				CREATE  TABLE mdl_dynamic_spdata (
				  `id` INT NOT NULL AUTO_INCREMENT ,
				  `userid` BIGINT(10) NOT NULL ,
				  contractedhours VARCHAR(15),
				  weeksrole VARCHAR(5),
				  weeksenrolled VARCHAR(5),
				  `courseid` BIGINT(10) NOT NULL ,
				  `checkpoint` TINYINT(1) NOT NULL ,
				  chkcompl TINYINT(1), 
				  `timeenrolled` BIGINT(10) NOT NULL ,
				  `coursemoduleid` BIGINT(10) ,
				  `labelname` VARCHAR(20) NOT NULL ,
				  `timemodified` BIGINT(10),
				  completionstate TINYINT(1),
				  PRIMARY KEY (`id`) ,
				  INDEX `u` (`userid` ASC) ,
				  INDEX `c` (`courseid` ASC) ,
				  INDEX `chk` (`checkpoint` ASC) ,
				  UNIQUE INDEX `u_c_m` (`userid` ASC, `courseid` ASC, `coursemoduleid` ASC) ,
				  INDEX `m` (`coursemoduleid` ASC) )";
		$success  = mysql_query($sql) or die(mysql_error());
		/*echo "<pre>";
					echo $sql;
					die;
*/

		//Get rules from the rule table
		/*
		id 	category_name 	categoryid 	contractedhours 	chk1 		chk2 		chk3 		chk4 		active
		1 	Retail Academy 	2 			full time 			3 weeks 	6 weeks 	3 months    6 months 	1
		2 	Retail Academy 	2 			part time 			6 weeks 	12 weeks	6 months    12 months 	1
		...

		*/
		$sqlrules = "SELECT categoryid,contractedhours,chk1,chk2,chk3,chk4,active FROM mdl_dynamic_storeprogress_rules WHERE active = 1";
		$result = mysql_query($sqlrules);

		if ($result){
			//Loop through each rule table
			$chklabels = array("First Checkpoint","Competent","Experienced","Final");
			//$chklabels = array("Welcome","Competent","Experienced","Final"); //debug

			$message = "";
			//Loop through each of the Rules
			while($row = mysql_fetch_assoc($result)) {
				if($row['active'] == 1){

					//Loop through each checkpoint for each Rule
					for($i = 1;$i <=4 ;$i++){
						//Checkpoint 1 - returns all users who are behind on Checkpoint 1
						if($row['chk' . $i] != ""){
							$chkpointtime= strtotime($row['chk' . $i],0); //convert chkpoint into timestamp for calculation
							$sqlchk = "
								INSERT INTO mdl_dynamic_spdata (userid,contractedhours,weeksrole,weeksenrolled,courseid,checkpoint,chkcompl,timeenrolled,coursemoduleid,labelname,timemodified,completionstate)
							";
							//Previous
							/*$sqlchk .= "
								SELECT 
									cmc.userid As 'userid',
									ud.contractedhours As 'contractedhours',
									cm.course  As 'courseid', 
									" . $i ." AS 'checkpoint',
									IF((CAST(cmc.timemodified AS SIGNED) - CAST(cc.timeenrolled AS SIGNED)) < " . $chkpointtime  . ", 1, 0) AS 'chkcompl',
									cc.timeenrolled AS 'timeenrolled',
									coursemoduleid As 'coursemoduleid',
									l.name AS 'labelname',
									cmc.timemodified,
									completionstate
								FROM mdl_course_modules_completion cmc
								INNER JOIN mdl_course_modules cm ON cm.id = cmc.coursemoduleid
								INNER JOIN mdl_label l ON l.course = cm.course AND l.id = cm.instance
								INNER JOIN mdl_course c ON c.id = cm.course
								INNER JOIN mdl_course_completions cc ON cc.userid = cmc.userid AND cc.course = cm.course
								INNER JOIN mdl_course_categories cat ON c.category = cat.id
								LEFT JOIN mdl_dynamic_userdata ud On ud.userid = cmc.userid
								WHERE module = 10
								AND cat.path LIKE '/" . $row['categoryid']  . "%' 
								AND l.name IN ('".$chklabels[$i-1]."') #Change labels
								AND ud.contractedhours = '" . $row['contractedhours']  . "' 
							";*/

							/* 
								chkcomp  statuses
								status: 0 = not completed not in time OR completed not in time = "No"
								status: 1 = not completed still in time "Blank"
								status: 2 = completed in time = "Yes"

								Update after issue log
								status: 0 = "No"    = not completed not in time
								status: 1 = "Blank" = not completed still in time
								status: 2 = "Yes"   = completed (any) acounts for both before and after allocated time 

							*/
							/*

							I think labels will only show as a completionstate of 1 thereofre the script does not need AND cmc.completionstate = 1. 
							Check this if errors are reported.

							*/
							$sqlchk .= "
								SELECT 
									cc.userid As 'userid',
									ud.contractedhours As 'contractedhours',
									ROUND((UNIX_TIMESTAMP() - ud.datestarted)/604800) AS 'weeksrole',
									ROUND((UNIX_TIMESTAMP() - cc.timeenrolled)/604800) AS 'weeksenrolled',
									cm.course  As 'courseid', 
									" . $i ." AS 'checkpoint',
									IF(cmc.timemodified IS NULL,
										IF(UNIX_TIMESTAMP() - CAST(cc.timeenrolled AS SIGNED) < " . $chkpointtime  . ", 1, 0) ,
										2
									)  AS 'chkcompl' ,
									cc.timeenrolled AS 'timeenrolled',
									coursemoduleid As 'coursemoduleid',
									l.name AS 'labelname',
									cmc.timemodified,
									completionstate
								FROM mdl_course_completions cc 
								INNER JOIN mdl_course_modules cm ON cc.course = cm.course
								INNER JOIN mdl_label l ON l.course = cm.course AND l.id = cm.instance
								INNER JOIN mdl_course c ON c.id = cc.course
								INNER JOIN mdl_course_categories cat ON c.category = cat.id
								LEFT JOIN mdl_dynamic_userdata ud On ud.userid = cc.userid
								LEFT JOIN mdl_course_modules_completion cmc ON cc.userid = cmc.userid AND cm.id = cmc.coursemoduleid
								WHERE module = 12 #Moodle 2.7 this has changed from 10 > 12
								AND cat.path LIKE '/" . $row['categoryid']  . "%' 
								AND l.name IN ('".$chklabels[$i-1]."') #Change labels
								AND ud.contractedhours = '" . $row['contractedhours']  . "' 
							";
							// echo "<pre>";echo $sqlchk;die;
							$success  = mysql_query($sqlchk) or die(mysql_error());
							if($success){
								$message .= " mdl_dynamic_spdata updated (catid:" . $row['categoryid'] . " | hours:" . $row['contractedhours'] . '\n';
							}

							//$success  = mysql_query($sql) or die(mysql_error());
						}
						/*echo "<pre>";
						echo $sqlchk ;
						die;*/
					}
					
				}
			}
		}

		die;
		
		//Mail Message. Could Eventually just email if an error happens using $error variable
		echo $mailMessage . "<br>";
		
		//Email me if there's an error
		if($error){
			error_log($mailMessage);
			$mail->Subject = 'ERROR! Database [' . $CFG->dbname  . '] Table [mdl_dynamic_spdata] Report';
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
	 createStoreProgressData();	
	
	
	

?>