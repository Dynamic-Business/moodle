<?php
define('CLI_SCRIPT', true);
require_once(dirname(__FILE__) .'\..\..\..\config.php');	



error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('memory_limit', '-1');
set_time_limit (50000); //To override 30 sec value from php.ini


$conn = new mysqli($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname); 

// Check connection
if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
} 


$courseSQL="SELECT course FROM mdl_next_hscourse";
$courseResult = $conn->query($courseSQL);

foreach ($courseResult as $course)
{
	
		//Fetch data from course table
		$sql = "SELECT 
				cc.course AS CourseNo, c.fullname AS CourseName,  u.firstname AS Firstname, u.lastname AS Lastname,  u.username AS PayRollNo, du.dept AS Department, du.storedetails AS StoreNo, du.area AS Area, from_unixtime(cc.timeenrolled) AS EnrolledDate, from_unixtime(cc.timestarted) AS TimeStarted, from_unixtime(cc.timecompleted) AS TimeCompleted, st.TimeStarted As FlashStartTime, st.TimeCompleted AS FlashFinishTime, 
				s.name AS QuizName, st.attempt AS Attempt, FlashResult, CASE WHEN cc.timecompleted IS NOT NULL THEN 'Pass' ELSE 'Failed Or Not Started' END AS CourseResult
				FROM mdl_user u 
				INNER JOIN (SELECT cc.course, cc.timeenrolled, cc.userid, cc.timestarted, cc.timecompleted FROM mdl_course_completions cc) cc  ON cc.userid=u.id AND cc.course=".$course['course']."
				INNER JOIN (SELECT c.id, c.fullname FROM  mdl_course c) c ON c.id=cc.course 
				INNER JOIN (SELECT du.userid, du.storedetails, du.area, du.dept FROM mdl_dynamic_userdata du)  du on u.id=du.userid
				LEFT JOIN (SELECT s.id, s.course, s.name FROM mdl_scorm s) s on s.course=cc.course
				LEFT JOIN (SELECT st.userid, st.scormid,  st.attempt, st.element, st.value As FlashResult, from_unixtime(timemodified) AS TimeCompleted, TimeStarted
							FROM mdl_scorm_scoes_track st
							inner join (SELECT t.userid AS USERSID, t.scormid as SCORMSID, t.attempt AS AttemptNo,  from_unixtime(value) AS TimeStarted 
										FROM mdl_scorm_scoes_track t 
										WHERE t.element='x.start.time') ss on (st.userid=USERSID AND st.scormid=SCORMSID AND st.attempt=AttemptNo)
							WHERE st.element='cmi.core.lesson_status') st on (st.scormid=s.id AND st.userid=cc.userid)
				ORDER BY Username;";
				
		$result = $conn->query($sql);
			
		
		$courseName='';


		$fileContent="CourseNo,CourseName,Firstname,Lastname,PayRollNo,Department,StoreNo,Area,EnrolledDate,TimeStarted,TimeCompleted,FlashStartTime,FlashFinishTime,QuizName,Attempt,FlashResult,CourseResult\n";
		foreach ($result as $data)
		{
			$fileContent.= "".$data['CourseNo'].",".$data['CourseName'].",".$data['Firstname'].",".$data['Lastname'].",".$data['PayRollNo'].",".$data['Department'].",".$data['StoreNo'].",".$data['Area'].",".$data['EnrolledDate'].",".$data['TimeStarted'].",".$data['TimeCompleted'].",".$data['FlashStartTime'].",".$data['FlashFinishTime'].",".$data['QuizName'].",".$data['Attempt'].",".$data['FlashResult'].",".$data['CourseResult']."\n";

			$courseName=$data['CourseName'];
		}

		
		$dir="\\\inst-pro-lms\\hs_data\\";
		
		$csv_filename = $dir.$courseName.".csv";
		
		
		$fd = fopen($csv_filename, "w");
		

		$fileContent=str_replace("\n\n","\n",$fileContent);

		fputs($fd, $fileContent);

	
		fclose($fd);
}

$conn->close();

?> 

