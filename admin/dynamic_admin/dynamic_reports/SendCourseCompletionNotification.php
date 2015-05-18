<?php
	
	define('CLI_SCRIPT', true);
require_once(dirname(__FILE__) .'\..\..\..\config.php');
	
	error_reporting(E_ALL);
    ini_set('display_errors', '1');

	
	// Create connection
	$conn = new mysqli($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname);
	// Check connection
	if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
	} 
	
	//Fetch data from course table
	$sql = "SELECT 
			u.username AS PayRollNo, cc.course AS CourseID, c.idnumber AS IDNUMBER, from_unixtime(cc.timestarted) AS StartDate, from_unixtime(cc.timecompleted) AS EndDate 
			FROM mdl_course_completions cc
			INNER JOIN  mdl_user u ON cc.userid=u.id 
			INNER JOIN mdl_course c ON c.id=cc.course AND TRIM(IFNULL(c.idnumber,'')) <> ''
			WHERE from_unixtime(cc.timecompleted) > (SELECT from_unixtime(coursecompletiontime) from mdl_next_notification) order by EndDate;";
	$result2 = $conn->query($sql);
	
	//To save a date of last record which we are sending 
	$numRows=$result2->num_rows;
	

	try {

	// Create a new soap client based on the service's metadata (WSDL)
	$client = new SoapClient("http://end-fdws01.next-uk.next.loc:82/HRIntegrationService/NLPNotificationIntegrationService.svc?wsdl", array('soap_version' => 'SOAP_1_2',
         'location'=>'http://end-fdws01.next-uk.next.loc:82/HRIntegrationService/NLPNotificationIntegrationService.svc'));
	
	
	
	$courseCompletion = array();
	
	$count=0;
	
	$lastDate='NOW()';
	
   //build array of data to send to HR System.
	 foreach ($result2 as $row) 
	  {
		$date1 = new DateTime($row['StartDate']);
		$date2 = new DateTime($row['EndDate']);
	  
		$startDate=date_format($date1, 'Y-m-d');
		$endDate=date_format($date2, 'Y-m-d');
		
	
		$params=array("payrollNo" => $row['PayRollNo'], "code" => $row['IDNUMBER'], "startDate" => $startDate, "endDate" => $endDate);
		
	    $client->CourseCompleted($params);
	  
	   $count++;
	   if($numRows==$count)
			$lastDate=$row['EndDate'];
	   else
			$lastDate='NOW()';
	  }
	  

	
	//Update last time course data sent coulmn
	if($lastDate!='NOW()')
		$UpdateSQL="UPDATE mdl_next_notification SET coursecompletiontime=UNIX_TIMESTAMP('".$lastDate."');  ";
	else
		$UpdateSQL="UPDATE mdl_next_notification SET coursecompletiontime=UNIX_TIMESTAMP(".$lastDate.");  ";

	$conn->query($UpdateSQL);
	
	}
	catch(Exception $e) {	 
	    print_r($e->getMessage());
	}
	
	$conn->close();

?> 
  