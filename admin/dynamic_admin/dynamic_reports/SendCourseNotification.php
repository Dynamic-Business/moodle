<?php
	
	//Database config file
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
			c.id, fullname, idnumber, from_unixtime(timecreated), courseID
			FROM mdl_course c
			RIGHT JOIN mdl_next_notification 
			ON 	(c.id > courseID AND (TRIM(IFNULL(idnumber,'')) <> ''));";
	$result2 = $conn->query($sql);
	
	//To save a id of last record which we are sending 
	$numRows=$result2->num_rows;
		
	try {

	// Create a new soap client based on the service's metadata (WSDL)
	$client = new SoapClient("http://end-fdws01.next-uk.next.loc:82/HRIntegrationService/NLPNotificationIntegrationService.svc?wsdl", array('soap_version' => 'SOAP_1_2',
         'location'=>'http://end-fdws01.next-uk.next.loc:82/HRIntegrationService/NLPNotificationIntegrationService.svc'));
	
	
	$courses = array();
	$count=0;
	
	
	
   //build array of data to send to HR System.
	 foreach ($result2 as $row) 
	  {
		
		if($row['id']!=null || $row['id']!='')
		{
			$params=array("code" => $row['idnumber'], "courseDescription" => $row['fullname']);
							
			$client->CourseCreated($params);
					  
		   $count++;
		   if($numRows==$count)
				$lastID=$row['id'];
		   else
				$lastID=$row['courseID'];
		}
		else
			$lastID=$row['courseID'];
	  }
	  
	
	//Here because of right join $lastID variable will always have some value. So this query will work fine.
		
	//Update last time course data sent coulmn
	$UpdateSQL="UPDATE mdl_next_notification SET courseID=".$lastID.";";
	  
	$conn->query($UpdateSQL);
	  
 }
	catch(Exception $e) {
	   print_r($e->getMessage());
	}
	
	
	
	$conn->close();

?> 
  