<?php
	
/*

Pay Increase Script for Next

=== Setup ===
pay_increase_audit should be setup from the start

CREATE  TABLE `next2`.`pay_increase_audit` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `idnumber` VARCHAR(25) NOT NULL ,
  `date_of_inclusion` VARCHAR(11) NOT NULL ,
  `stage` VARCHAR(12) NULL ,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_st` (`idnumber` ASC, `stage` ASC) );

- Ensure category id below is set correctly - this should be the category containing all Academy courses
- Ensure file paths are correct
- Ensure location is writable to


=== What happens in this script ===
- First a temporary table is created which contains all data for all 6 of the conditions. Each condition data is inserted into this table through the loop below.
This table contains all the fields needed for both the csv file and audit table. A user should only ever appear once so a uniqe id is added on the audit log and 
temp table - this way INSERT IGNORE can be used.

- The script ignores all user/state combinations in the audit log by a LEFT JOIN then a where statement 'AND pa.idnumber IS NULL'. Some users will appear twice in
 audit log as they will fall under seperate criteria.

- The date of the final csv output is week commencing date, i.e. the Sunday.

- 2.7 UPDATE
Quiz module is now 16 not 13. Code changed


=== For Testing/Debugging ===
- Rememebr that users will not show if they are already in the audit log //UPDATE NO LONGER THE CASE - they can appear twice under different criteria, but no more
- Copy the site to local
- You can run 'DELETE FROM pay_increase_audit;' To clear the audit log. 
- Run the script in the browser at http://localhost/next/admin/dynamic_admin/_create/create-pay-increases.php
- remember to run cron after going through tests
- If users aren't appearing, check that their userdata is correct i.e. jobcode= 'scon', contractedhours = 'part time' / 'full time' etc...


 === Conditions ===
 num 	is18	is21	contracted_hours 	datestarted 	experiencedquiz 	finalquiz 	
 1 		TRUE 	FALSE 	Part Time 			> 6 Months 		TRUE 				FALSE
 2 		TRUE 	FALSE 	Full Time 			> 3 Months 		TRUE 				FALSE
 3 		FALSE 	FALSE 	Part Time 			> 12 Months 	TRUE 				TRUE
 4 		TRUE 	FALSE 	Part Time 			> 12 Months 	TRUE 				TRUE
 5 		FALSE  	FALSE 	Full Time 			> 6 Months 		TRUE 				TRUE
 6 		TRUE 	FALSE 	Part Time 			> 6 Months 		TRUE 				TRUE


*/
	define('CLI_SCRIPT', true);

	//config
	$dbscript = TRUE;
	require_once(dirname(__FILE__) . '\..\..\..\config.php'); //main moodle config
	//echo dirname(__FILE__) . '\..\..\..\config.php';

	$csvPath = 'C:\inetpub\wwwroot\moodle\admin\dynamic_admin\_payincrease';
	$csvPath = dirname(__FILE__) . '\..\_payincrease';
	//$csvPath   = 'C:\xampp\htdocs\next2\admin\dynamic_admin\_payincrease';
	$academyCatId = 2;
	//
	$con = mysql_connect($CFG->dbhost ,$CFG->dbuser ,$CFG->dbpassword);
	mysql_select_db($CFG->dbname, $con);

//

//Create the Temp Table - both the population of the csv file and audit log is pulled form this temp table
for($i=1;$i<=6;$i++){
	if($i == 1){
		$sql = "DROP TABLE IF EXISTS pay_increase_data";
		$success  = mysql_query($sql) or die(mysql_error());
		//$sql = "CREATE TEMPORARY TABLE pay_increase_data (";
		$sql = "CREATE TABLE pay_increase_data (";
		$sql .= getSQL($i);
		$sql .= ")";
		//debug
		/*echo "<pre>" . $sql . "</pre>";
		die;*/
		$success  = mysql_query($sql) or die(mysql_error());
		//$sql = "ALTER TABLE pay_increase_data ADD UNIQUE INDEX idnumber (idnumber ASC);";
		$sql = "
			ALTER TABLE `pay_increase_data` ADD COLUMN `id` INT(11) NOT NULL AUTO_INCREMENT  FIRST , 
			ADD PRIMARY KEY (`id`) , 
			ADD UNIQUE INDEX `id_stage` (`userid` ASC, `stage` ASC) ;";

		$success  = mysql_query($sql) or die(mysql_error());
	}else{
		$sql = "
			INSERT IGNORE INTO pay_increase_data (
			userid,
		    idnumber,
		    first_name,
		    last_name,
		    date_started,
		    job_code,
		    current_job_grade,
		    over18,
		    over21,
		    contracted_hours,
		    Experienced_Quiz,
		    Final_Quiz,
		    course_name,
		    date_of_inclusion,
        	stage
			)(" . getSQL($i) . ")";
		//debug
		/*echo $sql;
		die;*/
		$success  = mysql_query($sql) or die(mysql_error());
	}
}

//Create/update audit log
$sql_select = "
	SELECT 
		idnumber AS 'payroll_no',
		date_of_inclusion,
		stage
	FROM pay_increase_data
";

$sql = "INSERT IGNORE INTO pay_increase_audit (idnumber,date_of_inclusion,stage) (" . $sql_select . ")";
$success  = mysql_query($sql) or die(mysql_error());

//Query for final CSV output 
$sql = "
	SELECT 
		idnumber AS 'payroll_no',
		first_name,
		last_name,
		date_started,
		job_code,
		current_job_grade,
		contracted_hours,
		IF(over18 = 1,'Yes','No') AS 'profile_field_isover18',
		IF(over21 = 1,'Yes','No') AS 'profile_field_isover21',
		# IF(Final_Quiz IS NULL,'Experienced','Final') AS 'stage'
		stage
		FROM pay_increase_data";

//$sql .= ($condition == 1 || $condition == 2) ? "'Experienced' AS 'stage' " : "'Final' AS 'stage'";

createData($sql);


function getSQL($condition){
	global $CFG, $db, $query,$academyCatId;
	$final = 1;
	switch ($condition){
		case 1:
			$stage = "'Experienced'";
			$final = 0; //To ensure that the Final col says 0 on the first 2 criteria
			$condition_sql = "
				AND over18.data = 1
				AND ud.contractedhours = 'Part Time'
				AND DATE_SUB(NOW(),INTERVAL 6 MONTH) >= FROM_UNIXTIME(datestarted)
				AND eq_comps.Experienced_Quiz IN (1,2) #AND Final_Quiz = 0
				";
		break;
		case 2:
			$stage = "'Experienced'";
			$final = 0; //To ensure that the Final col says 0 on the first 2 criteria
			$condition_sql = "
				AND over18.data = 1
				AND ud.contractedhours = 'Full Time'
				AND DATE_SUB(NOW(),INTERVAL 3 MONTH) >= FROM_UNIXTIME(datestarted)
				AND eq_comps.Experienced_Quiz IN (1,2) #AND Final_Quiz = 0
				";
		break;
		case 3:
			$stage = "'Final'";
			$condition_sql = "
				AND over18.data = 0
				AND ud.contractedhours = 'Part Time'
				AND DATE_SUB(NOW(),INTERVAL 12 MONTH) >= FROM_UNIXTIME(datestarted)
				AND eq_comps.Experienced_Quiz IN (1,2) AND Final_Quiz IN (1,2)
				";
		break;
		case 4:
			$stage = "'Final'";
			$condition_sql = "
				AND over18.data = 1
				AND ud.contractedhours = 'Part Time'
				AND DATE_SUB(NOW(),INTERVAL 12 MONTH) >= FROM_UNIXTIME(datestarted)
				AND eq_comps.Experienced_Quiz IN (1,2) AND Final_Quiz IN (1,2)
				";
		break;
		case 5:
			$stage = "'Final'";
			$condition_sql = "
				AND over18.data = 0
				AND ud.contractedhours = 'Full Time'
				AND DATE_SUB(NOW(),INTERVAL 6 MONTH) >= FROM_UNIXTIME(datestarted)
				AND eq_comps.Experienced_Quiz IN (1,2) AND Final_Quiz IN (1,2)
				";
		break;
		case 6:
			$stage = "'Final'";
			$condition_sql = "
				AND over18.data = 1
				AND ud.contractedhours = 'Full Time'
				AND DATE_SUB(NOW(),INTERVAL 6 MONTH) >= FROM_UNIXTIME(datestarted)
				AND eq_comps.Experienced_Quiz IN (1,2) AND Final_Quiz IN (1,2)
				";
		break;
	}
	$sql = "
		SELECT 
		    u.id AS 'userid',
		    u.idnumber,
		    u.firstname AS 'first_name',
		    lastname AS 'last_name',
		    FROM_UNIXTIME(ud.datestarted,'%d/%m/%Y') AS 'date_started',
		    ud.jobcode AS 'job_code',
		    ud.jobgrade AS 'current_job_grade',
		    over18.data AS 'over18',
		    over21.data AS 'over21',
		    ud.contractedhours AS 'contracted_hours',
		    eq_comps.Experienced_Quiz,
		    '" . $final . "' AS 'Final_Quiz',
		    eq_comps.course_name,
		    DATE_FORMAT(NOW(),'%d/%m/%Y') AS 'date_of_inclusion', " .
        	# IF(fq_comps.Final_Quiz IS NULL,'Experienced','Final') AS 'stage'
		    $stage . " As 'stage' 
		FROM mdl_user u
		INNER JOIN(
		    SELECT 
		        q.id AS 'quiz_id',
		        q.name AS 'quiz_name',
		        c.fullname As 'course_name' ,
		        cm.id AS 'module_id',
		        q.name,
		        cmc.completionstate AS 'Experienced_Quiz',
		        cmc.userid
		    FROM mdl_course_categories cats
		    INNER JOIN mdl_course c ON c.category = cats.id
		    INNER JOIN mdl_quiz q ON q.course = c.id
		    INNER JOIN (SELECT * FROM mdl_course_modules WHERE module = 16) cm ON c.id = cm.course AND cm.instance = q.id
		    INNER JOIN mdl_course_modules_completion cmc ON cmc.coursemoduleid = cm.id
		    WHERE cats.path LIKE '/".$academyCatId."%'
		    AND q.name = 'Experienced Quiz'
		    AND cmc.completionstate IN (1,2)
		) eq_comps ON eq_comps.userid = u.id
		-- Final Quiz
		LEFT JOIN (
		    SELECT 
		        q.id AS 'quiz_id',
		        q.name AS 'quiz_name',
		        c.fullname As 'course_name',
		        cm.id AS 'module_id',
		        q.name,
		        cmc.completionstate AS 'Final_Quiz',
		        cmc.userid
		    FROM mdl_course_categories cats
		    INNER JOIN mdl_course c ON c.category = cats.id
		    INNER JOIN mdl_quiz q ON q.course = c.id
		    INNER JOIN (SELECT * FROM mdl_course_modules WHERE module = 16) cm ON c.id = cm.course AND cm.instance = q.id
		    INNER JOIN mdl_course_modules_completion cmc ON cmc.coursemoduleid = cm.id 
		    WHERE cats.path LIKE '/".$academyCatId."%'
		    AND q.name = 'Final Quiz'
		    AND cmc.completionstate IN (1,2)
		) fq_comps ON fq_comps.userid = u.id 
		INNER JOIN mdl_dynamic_userdata ud ON u.id = ud.userid
		INNER JOIN mdl_user_info_data AS over18 ON u.id = over18.userid
		INNER JOIN mdl_user_info_data AS over21 ON u.id = over21.userid
		LEFT JOIN pay_increase_audit pa ON u.idnumber = pa.idnumber AND pa.stage = " . $stage . "
		WHERE u.deleted != 1
		AND pa.idnumber IS NULL
		AND jobcode = 'SCON'
		AND over18.fieldid = 8 
		AND over21.fieldid = 9 
		AND over21.data = 0 " .
		$condition_sql
		. " GROUP BY userid
	";
	return $sql;

}

function createData($sql){
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
	echo "$csvContent";
	//Write the csv file and store in $csvPath
	$file = 'PF';
	//script runs monday mornign however date need to be the Sunday.
	$oneWeekAgo = date("dmy",strtotime( '-8 day' , time()) ); 
	$filename = $file.$oneWeekAgo.".csv.";
	file_put_contents($csvPath . "/" . $filename, $csvContent);
	echo $csvPath . "/" . $filename;
}


?>