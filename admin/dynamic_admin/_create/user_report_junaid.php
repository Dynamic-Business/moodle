<?php


//config
$dbscript = TRUE;
require_once(dirname(__FILE__) . '\..\..\..\config.php'); //main moodle config
//echo dirname(__FILE__) . '\..\..\..\config.php';

$csvPath = 'C:\inetpub\wwwroot\moodle\admin\dynamic_admin\_payincrease';

$academyCatId = 2;

$con = mysql_connect($CFG->dbhost ,$CFG->dbuser ,$CFG->dbpassword);
mysql_select_db($CFG->dbname, $con);


//Query for Experienced CSV output 
$sql = "
	SELECT 
    cmc.userid,
    u.username,
    u.firstname,
    u.lastname,
    s.id AS 'scorm_id',
    s.name AS 'scorm_name',
    c.fullname As 'course_name' ,
    cm.id AS 'module_id',
    s.name,
    cmc.completionstate AS 'Experienced_Quiz State',
    FROM_UNIXTIME(cmc.timemodified,'%d-%m-%Y') AS 'scorm completion date'
    FROM mdl_course_categories cats
    INNER JOIN mdl_course c ON c.category = cats.id
    INNER JOIN mdl_scorm s ON s.course = c.id
    INNER JOIN (SELECT * FROM mdl_course_modules WHERE module = 15) cm ON c.id = cm.course AND cm.instance = s.id
    INNER JOIN mdl_course_modules_completion cmc ON cmc.coursemoduleid = cm.id
    INNER JOIN mdl_user u ON u.id = cmc.userid
    WHERE cats.path LIKE '/2%'
    AND s.name = 'Experienced Quiz'
    AND cmc.completionstate IN (1,2)
	order by 'scorm completion date'";


$file = 'Experienced';

createData($sql, $file);



//Query for Final CSV output 
$sql = "


	SELECT 
    cmc.userid,
    u.username,
    u.firstname,
    u.lastname,
    s.id AS 'scorm_id',
    s.name AS 'scorm_name',
    c.fullname As 'course_name' ,
    cm.id AS 'module_id',
    s.name,
    cmc.completionstate AS 'Experienced_Quiz State',
    FROM_UNIXTIME(cmc.timemodified,'%d-%m-%Y') AS 'scorm completion date'
    FROM mdl_course_categories cats
    INNER JOIN mdl_course c ON c.category = cats.id
    INNER JOIN mdl_scorm s ON s.course = c.id
    INNER JOIN (SELECT * FROM mdl_course_modules WHERE module = 15) cm ON c.id = cm.course AND cm.instance = s.id
    INNER JOIN mdl_course_modules_completion cmc ON cmc.coursemoduleid = cm.id
    INNER JOIN mdl_user u ON u.id = cmc.userid
    WHERE cats.path LIKE '/2%'
    AND s.name = 'Final Quiz'
    AND cmc.completionstate IN (1,2)
	order by 'scorm completion date'";

$file = 'Final';

createData($sql, $file);


function createData($sql, $file){
	global $CFG, $db, $query, $csvPath;
	
	/*echo "<pre>";
	echo $sql;
	echo "</pre>";
	die;*/
	$csvContent = "";
	$data = mysql_query($sql) or die(mysql_error()); 
	$numFields = mysql_num_fields($data);
	
	for($i=0;$i<$numFields;$i++){
		$csvContent .= 	mysql_field_name($data,$i);
		if($i != ($numFields-1)){
			$csvContent .= ",";
		}
	}
	$csvContent .= "\r\n";
	while ($row = mysql_fetch_array($data)) {
		//$csvContent .= "\r\n";
		
		for($i=0;$i<$numFields;$i++){
		
			$csvContent .= trim($row[$i]);
			
			if($i != ($numFields-1)){
				$csvContent .= ",";
			}
		}
		$csvContent .= "\r\n";
	}
	//echo "$csvContent";
	//Write the csv file and store in $csvPath
	//script runs monday morning
	$oneWeekAgo = date("dmy",strtotime( '-2 day' , time()) ); 
	$filename = $file.".csv.";
	file_put_contents($csvPath . "/" . $filename, $csvContent);
	echo $csvPath . "/" . $filename;
}


?>