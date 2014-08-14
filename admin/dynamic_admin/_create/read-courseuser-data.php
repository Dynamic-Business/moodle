<?php

/*

Course/User data read import for Next
- 

=== Setup ===
- SETUP courses in new

=== What happens in this script ===
The script reads the csv file which has been outputted from the external site and imported and copied to
C:/course_data/course_data.csv by NEXT. This script must be setup as a scheduled task each Sunday night at 10:30pm

The csv file contains all user/course completions from the last 2 weeks. This scrpts will ignore all course completions that have been previously added to course_completions table.

The script will first look for a manual enrolment for a course.
It will then enrol the user onto the course using the manual enrolment id, unless the enrolment already exists.
It will then add the course completion for that course.


=== For Testing/Debugging ===


*/

//config ----------------------------------------------------------
define('CLI_SCRIPT', true);
if(php_sapi_name() != 'cli'){
    echo "Browser access denied.";
    die();
}

require_once(dirname(__FILE__) . '/../../../config.php'); //main moodle config
//require_once(dirname(__FILE__) . '/../../../config-local.php'); //local testing

$csvReadPath   = "C:\Course_Data\course_data.csv"; //location on Next server
//$csvReadPath   = "course_data_test.csv"; //local testing

$logfile = "C:\Course_Data\logs\log.txt";
//$logfile = "log.txt";

//echo "called";
//writeToLog ("test called");
//die;

$con = mysql_connect($CFG->dbhost ,$CFG->dbuser ,$CFG->dbpass);

mysql_select_db($CFG->dbname, $con);

//config end ------------------------------------------------------
//-----------------------------------------------------------------


readData();

function readData(){
	global $csvReadPath,$CFG,$con,$db;
	$logstr = "";
	//read the csv file in C:\coure_data\course_data.csv

    $handle = fopen($csvReadPath,"r");
    $csvArray = $fields = array(); 
    $fields = fgetcsv($handle);
    $i = 0;

    //loop through the csv file and convert into Assoc Array
	if ($handle) {
		while (($row = fgetcsv($handle)) !== false) {
		    if (empty($fields)) {
		        $fields = $row;
		        continue;
		    }
		    foreach ($row as $k=>$value) {
		        $csvArray[$i][$fields[$k]] = $value;
		    }
		    $i++;
		}
		if (!feof($handle)) {
		    echo "Error: unexpected fgets() fail\n";
		}
	fclose($handle);
	}
	/*echo "<pre>";
	var_dump($csvArray);
	echo "</pre>";*/
	//echo $csvArray[1]['username'];
	$insert_count = 0;
	$enrol_count = 0;
	$ra_count = 0; //role assignments
	foreach ($csvArray as $line) {

		$sql = "SELECT id FROm mdl_user WHERE username = '" . $line['username'] . "'";
		$result = mysql_query($sql) or die(mysql_error());
		$row = mysql_fetch_array($result);
		$user_id = $row['id'];

		$sql = "SELECT id FROm mdl_course WHERE idnumber = '" . $line['idnumber'] . "'";
		$result = mysql_query($sql) or die(mysql_error());
		$row = mysql_fetch_array($result);
		$course_id = $row['id'];

		$timeenrolled = $line['timeenrolled'];
		$timestarted = $line['timestarted'];
		$timecompleted = $line['timecompleted'];
		$timestamp = time();

		//echo $sql . "<br>";

		if($user_id != "" && $course_id != ""){
			// Get The enolment id
			$sql = "
				SELECT id FROM mdl_enrol WHERE courseid = " . $course_id . " AND enrol = 'manual'
			";
			$result = mysql_query($sql) or die(mysql_error());
			$row = mysql_fetch_array($result);
			$enrol_id = $row['id'];

			//If no manual enrolment exists for the course, then don't do anything.
			if($enrol_id != ""){	
				//See if a manual enrolment already exists for the user and enrol id
				$sql = "
					SELECT * FROM mdl_user_enrolments WHERE enrolid = " . $enrol_id . " AND userid = " . $user_id;
				$result = mysql_query($sql) or die(mysql_error());
				if (mysql_num_rows($result) == 0){
					//Enrol the user on to the course using the above enrol_id
					$sql = "
						INSERT IGNORE INTO mdl_user_enrolments (enrolid,userid,timestart,timeend,timecreated,timemodified) VALUES 
						(" . $enrol_id . "," . $user_id . "," . $timeenrolled . ",0," . $timestamp . "," . $timestamp . ")";
					mysql_query($sql) or die(mysql_error());
					$ar = mysql_affected_rows();
					if ($ar >= 1){
						$enrol_count++;
					}

					//next get the context id needed for insert of mdl_role_assignments
					$sql = "
						SELECT id FROm mdl_context WHERE contextlevel = 50 AND instanceid = $course_id";
					$result = mysql_query($sql) or die(mysql_error());
					$row = mysql_fetch_array($result);
					$context_id = $row['id'];

					if($context_id != ""){
						//Add row to mdl_role_assignments if it doesn't already exist
						$sql = "SELECT * FROm mdl_role_assignments WHERE contextid = $context_id AND userid = $user_id";
						$result = mysql_query($sql) or die(mysql_error());
						if (mysql_num_rows($result) == 0){
							$sql = "INSERT IGNORE INTO mdl_role_assignments (roleid,contextid,userid) VALUES (5,$context_id,$user_id)";
							mysql_query($sql) or die(mysql_error());
							$ar = mysql_affected_rows();
							if ($ar >= 1){
								$ra_count++;
							}
						}
					}

				}
				// See if a course completion already exists
				$sql = "
					SELECT * FROM mdl_course_completions 
					WHERE userid = " . $user_id . " 
					AND course = " . $course_id ;
				$result = mysql_query($sql) or die(mysql_error());
				//echo  $result;
				//die;
				// If record doesn't exists then add record
				if (mysql_num_rows($result) == 0){
					$sql = "
					INSERT IGNORE INTO mdl_course_completions (userid,course,timeenrolled,timestarted,timecompleted) VALUES
					(" . $user_id . "," . $course_id . "," . $timeenrolled . "," . $timestarted . "," . $timecompleted . ")";
					mysql_query($sql) or die(mysql_error());
					$ar = mysql_affected_rows();
					if ($ar >=1){
						$insert_count++;
					}
					$logstr .= "userid:" . $user_id . " | courseid:" . $course_id . " course completion updated";
					echo "userid:" . $user_id . " | courseid:" . $course_id . " course completion updated.<br>";
				}
			}

		}

	}
	echo $enrol_count . " user enrolments added.<br>";
	echo $ra_count . " role assginments records added.<br>";
	echo $insert_count . " course completion records added.<br>";

	$txt = $enrol_count . " user enrolments added.\r\n". $ra_count . " role assignments added.\r\n". $insert_count . " course completion records added.";
	writeToLog ($txt);
}

function writeToLog($msg,$error=false){
	global $logfile;
	$fh = fopen($logfile, 'a') or die("can't open file");
	$stringData = date('l dS \of F Y h:i:s A') . "";
	if($error){
		$stringData .= "\r\nERROR : " . $msg;
	}else{
		$stringData .= "\r\n" . $msg;
	}
	$stringData .= "\r\n--------------------\r\n";
	fwrite($fh, $stringData);
	fclose($fh);
}

?>