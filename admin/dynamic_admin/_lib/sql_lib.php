<?php
	
	//setup some variables
	$paginationRequired = false; 
	$rows = 0;
	$statusData;

	//-------------------------------------------------------------------------------------------------------------------------------------------------------------------
	// 1. Switch Statement within report.php calls one of these, depending on query string param. Calls functions below to get the query.
	//-------------------------------------------------------------------------------------------------------------------------------------------------------------------
	
	function reportbyGroup($groups,$courses="",$status="",$orderby="u.lastname"){
		global $DB, $datepickerto, $datepickerfrom, $resultsPerPage, $rows, $selfPageRef;
		if($status[0] == "Not Started"){
			//build the exception report
			$sql = buildExceptionsByGroupQuery($groups,$courses,$orderby);
		}else{
			//run the normal report
			$sql = buildReportByGroupQuery($groups,$courses,$orderby,$status);
		}
		$html = "";
		$_SESSION['query'] = $sql; //used for the download page
		$html = buildHTMLTable($sql);
		return $html;
	}
	function reportByMoodleQuiz($groups,$courses="",$status=array(),$orderby="Last_Name"){
		global $DB, $datepickerto, $datepickerfrom, $resultsPerPage, $rows, $selfPageRef;
		if($status[0] == "Not Started"){
			//build the exception report
			$sql = buildExceptionsMoodleQuizQuery($groups,$courses,$orderby);
		}else{
			//run the normal report
			$sql = buildReportByMoodleQuizQuery($groups,$courses,$orderby);
		}
		$html = "";
		$_SESSION['query'] = $sql; //used for the download page
		$html = buildHTMLTableQuizzes($sql);
		return $html;
	}
	function reportByHR($courses="",$status=array(),$profile_field="",$profiledata=array(),$orderby="Last_Name"){
		global $DB, $datepickerto, $datepickerfrom, $resultsPerPage, $rows, $selfPageRef;
		if($status[0] == "Not Started"){
			//build the exception report
			$sql = buildExceptionsHRQuery($courses,$profile_field,$profiledata,$orderby);
		}else{
			//run the normal report
			$sql = buildReportByHRQuery($courses,$status,$profile_field,$profiledata,$orderby);
		}
		$html = "";
		$_SESSION['query'] = $sql; //used for the download page
		$html = buildHTMLTable($sql);
		return $html;	
	}
	function reportByHRQuiz($courses="",$status=array(),$profile_field="",$profiledata=array(),$orderby="Last_Name"){
		global $DB, $datepickerto, $datepickerfrom, $resultsPerPage, $rows, $selfPageRef;
		if($status[0] == "Not Started"){
			//build the exception report
			$sql = buildExceptionsHRQuizQuery($courses,$profile_field,$profiledata,$orderby);
		}else{
			//run the normal report
			$sql = buildReportHRQuizQuery($courses,$status,$profile_field,$profiledata,$orderby);
		}
		$html = "";
		$_SESSION['query'] = $sql; //used for the download page
		$html = buildHTMLTableQuizzes($sql);
		return $html;
	}
	//-------------------------------------------
	//New F2F classroom reports
	function reportByHRClassroom($courses="",$status=array(),$profile_field="",$profiledata=array(),$session_status ){
		global $DB, $datepickerto, $datepickerfrom, $resultsPerPage, $rows, $selfPageRef;

		$sql = buildReportHRClassroomQuery($courses,$status,$profile_field,$profiledata,$session_status);

		$html = "";
		$_SESSION['query'] = $sql; //used for the download page
		$html = buildHTMLTableQuizzes($sql);
		return $html;
	}
	function reportByClassroom($groups=array(),$courses="",$status=array(),$session_status){
		global $DB, $datepickerto, $datepickerfrom, $resultsPerPage, $rows, $selfPageRef;

		$sql = buildReportByClassroomQuery($groups,$courses,$status,$session_status);

		$html = "";
		$_SESSION['query'] = $sql; //used for the download page
		$html = buildHTMLTableQuizzes($sql);
		return $html;
	}
	//--------------------------------------------
	//New Course Reports from 7.3
	function reportByCourseHR($courses="",$status=0,$profile_field="",$profiledata=array(),$orderby="Last_Name"){
		if($status == 3){
			//Build the exception report
			$sql = buildExceptionsCourseHRQuery($courses,$profile_field,$profiledata,$orderby);
			
		}else{
			//Run the normal report
			$sql = buildReportByCourseHRQuery($courses,$status,$profile_field,$profiledata,$orderby);
		}
		$html = "";
		$_SESSION['query'] = $sql; //used for the download page
		$html = buildHTMLTable($sql);
		return $html;
	}
	function reportByCourse($groups,$courses="",$status=0,$orderby="Last_Name"){
		if($status == 3){
			//Build the exception report
			$sql = buildExceptionsCourseGroupQuery($groups,$courses,$orderby);
			
		}else{
			//Run the normal report
			$sql = buildReportByCourseGroupQuery($groups,$courses,$status,$orderby);
		}
		$html = "";
		$_SESSION['query'] = $sql; //used for the download page
		$html = buildHTMLTable($sql);
		return $html;
	}
	//New for 7.6 (individual user report
	function reportByUser($userids){
		//echo ($userids);
		$sql = buildReportbyUser($userids);
		$html = "";
		$_SESSION['query'] = $sql; //used for the download page
		$html = buildHTMLTable($sql);
		return $html;

	}
	//New for 7.7 (Overview report)
	function reportByOverview($courses="",$profile_field="",$profiledata=array(),$overviewtype){
		global $DB, $datepickerto, $datepickerfrom, $resultsPerPage, $rows, $selfPageRef;
		$html = "";
		if($overviewtype == "accumulative"){
			$html = buildReportByOverviewAcc($courses,$profile_field,$profiledata);
		}else if($overviewtype == "separate") {
			$html = buildReportByOverviewSep($courses,$profile_field,$profiledata);
		}
		return $html;	
	}
	
	//--------------------------------------------
	//simpler function doesn't call anything below
	function reportViewGroupManagers(){
		global $page, $resultsPerPage ,$DB, $selfPageRef ;
		$sql = "SELECT dmg.id,g.name, u.firstname,u.lastname
				FROM mdl_dynamic_managers_group AS dmg
				INNER JOIN (
					SELECT id,firstname,lastname
					FROM mdl_user
				) AS u ON u.id = dmg.userid
				INNER JOIN (
					SELECT id,name
					FROM mdl_dynamic_group
				) AS g ON g.id = dmg.groupid
				ORDER BY g.name, userid";
		//echo($sql);
		$data = $DB->get_records_sql($sql);
		//print_r($data);
		
		if($data){ 
			$html = "<table cellpadding='0' cellspacing='0' border='0' class='display' id='styled-table'>";
			$html .= "<thead>";
			$html .= "<tr>";
			//Start at 1 because we don't want to show the db-id in the report, but we need for other functionality
			$html .= "<th>Group Name</th><th>Assigned Managers</th>";
			$html .= "</tr>";
			$html .= "</thead>";
			$html .= "<tbody>";
			
			foreach($data as $row) {
				$html .= "<tr>";
				$html .= "<td>" . $row->name . "</td>";
				$html .= "<td>" . $row->firstname . " " . $row->lastname  . "</td>";
				$html .= "</tr>";
				
			}
			$html .= "</tbody>";
			$html .= "</table>";
		}else{
			$html = FALSE;
		} 
		return $html;
	}

	function reportByLogins($logintype='logins'){
		global $page, $resultsPerPage ,$DB, $reportAdditionalColumns,$datepickerto, $datepickerfrom;
		foreach ($reportAdditionalColumns as $column){
			$udSelect .= ",ud." . $column . " " ;
		}

		switch ($logintype) {
			case 'logins':

				$sql = "
						SELECT 
							u.id,
							u.username,
							u.firstname,
							u.lastname,
							FROM_UNIXTIME(l.time,'%Y-%m-%d (%H:%m)') AS 'Login Date (y-m-d)'" .
							$udSelect  . "
						FROM mdl_log l
						INNER JOIN mdl_user u ON l.info = u.id
						INNER JOIN mdl_dynamic_userdata ud On u.id = ud.userid
						WHERE action = 'login'
						AND l.time >= "  . getDayStart(strtotime(convertDateUkToUS($datepickerfrom))) . "
				  		AND l.time <= "  . getDayEnd(strtotime(convertDateUkToUS($datepickerto)));
				//echo $sql;
				//$data = $DB->get_records_sql($sql);
				
				//return $data;

			break;
			case 'nologins':
				$sql = "
					SELECT 
							u.id,
							u.username,
							u.firstname,
							u.lastname" .
							$udSelect  . "
						FROM mdl_user u 
						INNER JOIN mdl_dynamic_userdata ud On u.id = ud.userid
						WHERE currentlogin = 0";
			break;
		}
		//echo $sql;
		$_SESSION['query'] = $sql; //used for the download page
		return buildHTMLTable($sql);

	}

	function reportByStoreProgress($groups,$courseid){
		global $page, $resultsPerPage ,$DB, $reportAdditionalColumns,$datepickerto, $datepickerfrom;
		$groupSql = getWhereGroupSQL($groups,"ug","groupid",true);
		$sql = "
			SELECT 
				cc.id,
			    ud.storedetails,
			    u.firstname,
			    u.lastname,
			    ud.dept AS 'dept',
			    ud.contractedhours AS 'hours',
			    ch2.weeksenrolled AS 'Weeks Enrolled',
			    #FROM_UNIXTIME(cc.timeenrolled,'%d/%m/%Y') AS 'Enrolled',
			    # cc.course,
			    #IF(ch1.chkcompl IS NULL,'',IF(ch1.chkcompl = 0,'No','Yes')) AS 'First Checkpoint',
			    #IF(ch2.chkcompl IS NULL,'',IF(ch2.chkcompl = 0,'No','Yes')) AS 'Competent',
			    #IF(ch3.chkcompl IS NULL,'',IF(ch3.chkcompl = 0,'No','Yes')) AS 'Experienced',
			    #IF(ch4.chkcompl IS NULL,'',IF(ch4.chkcompl = 0,'No','Yes')) AS 'Final',
				
				IF(ch1.chkcompl IS NULL,'',IF(ch1.chkcompl = 0,'No',IF(ch1.chkcompl = 1,'','Yes'))) AS 'First Checkpoint',
			    IF(ch2.chkcompl IS NULL,'',IF(ch2.chkcompl = 0,'No',IF(ch2.chkcompl = 1,'','Yes'))) AS 'Competent',
			    IF(ch3.chkcompl IS NULL,'',IF(ch3.chkcompl = 0,'No',IF(ch3.chkcompl = 1,'','Yes'))) AS 'Experienced',
			    IF(ch4.chkcompl IS NULL,'',IF(ch4.chkcompl = 0,'No',IF(ch4.chkcompl = 1,'','Yes'))) AS 'Final'

			FROM mdl_course_completions cc
			INNER JOIN mdl_dynamic_userdata ud ON ud.userid = cc.userid
			INNER JOIN mdl_user u ON u.id = cc.userid
			# Enrolled user only #
			INNER JOIN (
                    SELECT userid,e.courseid
                    FROM mdl_user_enrolments ue
                    INNER JOIN mdl_enrol e ON e.id = ue.enrolid
                    WHERE e.courseid = " . $courseid . " AND ue.status = 0 AND e.status = 0 AND (ue.timestart < UNIX_TIMESTAMP() || ue.timestart = 0) AND (ue.timeend > UNIX_TIMESTAMP() || ue.timeend = 0) GROUP BY userid,courseid
            ) ue ON cc.userid = ue.userid AND cc.course = ue.courseid
			LEFT JOIN (SELECT * FROM mdl_dynamic_spdata WHERE checkpoint=1) ch1 ON ch1.courseid = cc.course AND ch1.userid = cc.userid
			LEFT JOIN (SELECT * FROM mdl_dynamic_spdata WHERE checkpoint=2) ch2 ON ch2.courseid = cc.course AND ch2.userid = cc.userid
			LEFT JOIN (SELECT * FROM mdl_dynamic_spdata WHERE checkpoint=3) ch3 ON ch3.courseid = cc.course AND ch3.userid = cc.userid
			LEFT JOIN (SELECT * FROM mdl_dynamic_spdata WHERE checkpoint=4) ch4 ON ch4.courseid = cc.course AND ch4.userid = cc.userid
			INNER JOIN mdl_dynamic_usersgroups ug ON ug.userid = cc.userid 
			WHERE cc.course = " . $courseid . "
			AND " . $groupSql .  "
			AND ((ch1.chkcompl = 0) OR (ch2.chkcompl = 0) OR (ch3.chkcompl = 0) OR (ch4.chkcompl = 0)) # commentting out this line provides good debugging
			GROUP BY cc.userid #because user can be in more than one of the selected groups
			ORDER BY ud.storedetails, ud.dept,u.lastname
			";
/*
			AND (
			    (ch1.chkcompl = 0 OR ch1.chkcompl IS NULL) 
			    OR (ch2.chkcompl = 0 OR ch2.chkcompl IS NULL) 
			    OR (ch3.chkcompl = 0 OR ch3.chkcompl IS NULL) 
			    OR (ch4.chkcompl = 0 OR ch4.chkcompl IS NULL)
			) ";
*/
		// echo "<pre>";
		// echo $sql;die;
		$_SESSION['query'] = $sql; //used for the download page
		return buildHTMLTable($sql,TRUE);
	}



	//-------------------------------------------------------------------------------------------------------------------------------------------------------------------
	// 2. Functions above call these to build the actual SQL query
	//-------------------------------------------------------------------------------------------------------------------------------------------------------------------
	
	function buildReportByGroupQuery($groups,$courses,$orderby,$status){
		global $datepickerto, $datepickerfrom, $resultsPerPage, $page, $paginationRequired, $rows;
		$selectSQL = getSelectSQL();
		//Converts returned values from form into logical WHERE clauses for the query
		$courseSql = getWhereCourseSQL($courses,"c","id",true);
		$statusSql = getWhereStatusSQL($status);
		$pfieldSql = "";
		$groupSql = getWhereGroupSQL($groups,"ug","groupid",true);
		$profileFieldsSQL = getProfileDataSQL();
		
		//Select user details and attach all sco names
		$sql = $selectSQL . 
		"FROM mdl_user u
		INNER JOIN mdl_scorm_scoes_track st on u.ID = st.userid
		INNER JOIN mdl_scorm s on st.scormid = s.id
		INNER JOIN mdl_scorm_scoes sco on st.scoid = sco.id
		INNER JOIN (SELECT * FROM mdl_course c WHERE " . $courseSql . ") c ON s.course = c.id 
		INNER JOIN mdl_dynamic_userdata ud ON u.id = ud.userid " .
		
		"INNER JOIN (SELECT * FROM mdl_dynamic_usersgroups ug WHERE  " . $groupSql .  " GROUP BY userid) AS ugroup ON u.id = ugroup.userid "  .
		
		//Get SCO records data
		"WHERE st.element = 'cmi.core.lesson_status'
		AND st.id IN
		 (SELECT MAX(st1.id)
		  FROM mdl_scorm_scoes_track st1
		  WHERE st.userid = st1.userid
		  AND st.scormid = st1.scormid
		  AND st.scoid = st1.scoid
		  AND st1.element = 'cmi.core.lesson_status'" . 
		  $statusSql . 
		  " AND st.timemodified >= "  . getDayStart(strtotime(convertDateUkToUS($datepickerfrom))) . 
		  " AND st.timemodified <= "  . getDayEnd(strtotime(convertDateUkToUS($datepickerto))) .
		 ")" .
		" ORDER BY " . $orderby . ",c.fullname,s.name";	
		//echo $sql;
		return $sql;
	}
	
	
	
	
	function buildExceptionsByGroupQuery($groups,$courses,$orderby){
		global $datepickerto, $datepickerfrom, $resultsPerPage, $page, $paginationRequired, $rows,$reportAdditionalColumns;
		$selectSQL = getSelectExceptionsSQL();
		$courseSql = getWhereCourseSQL($courses,"c","id",true);
		$courseSql2 = getWhereCourseSQL($courses,"e","courseid",TRUE); // new code for enrolled users
		$pfieldSql = "";
		$groupSql = getWhereGroupSQL($groups,"ug","groupid",true);
		$profileFieldsSQL = getProfileDataSQL();
		
		//Select user details and attach all sco names
		$sql = $selectSQL . 
		
		"FROM (
			SELECT u.id,u.username,u.firstname,u.lastname," . implode(",",$reportAdditionalColumns)   . ",s.Course_ID,s.Training_Session,s.SCORM_ID,s.Module_Name,s.SCO_ID,s.Lesson_Name FROM 

				(SELECT c.id AS 'Course_ID' , c.fullname AS 'Training_Session', scorm.id AS 'SCORM_ID', scorm.name AS 'Module_Name',scoes.id AS 'SCO_ID',scoes.title AS 'Lesson_Name' 
				FROM (SELECT * FROM mdl_scorm_scoes WHERE scormtype = 'sco') AS scoes 
				INNER JOIN mdl_scorm scorm on scorm.id = scoes.scorm 
				INNER JOIN mdl_course c on scorm.course = c.id 
				WHERE " . $courseSql . ") s,

				(SELECT u.id,username,firstname,lastname," . implode(",",$reportAdditionalColumns)   . "
				FROM mdl_user u
				INNER JOIN mdl_dynamic_userdata ud ON u.id = ud.userid
				INNER JOIN (SELECT userid,groupid FROM mdl_dynamic_usersgroups ug WHERE  " . $groupSql .  " GROUP BY userid) AS ugroup ON u.id = ugroup.userid 
				
				) u "  .
    		") u " .
		
		//Get SCO records and attach to user info above
		// New code to only return enrolled users -----------------
		"INNER JOIN (
				SELECT userid,e.courseid
				FROM mdl_user_enrolments ue
				INNER JOIN (SELECT courseid,id FROM mdl_enrol WHERE status =0) AS e ON e.id = ue.enrolid
				WHERE " . $courseSql2 . " AND ue.status = 0 AND (ue.timestart < UNIX_TIMESTAMP() || ue.timestart = 0) AND (ue.timeend > UNIX_TIMESTAMP() || ue.timeend = 0) GROUP BY userid,courseid
		) ue ON ue.userid = u.id AND ue.courseid = u.Course_ID " .
		// --------------------------------------------------------
		
		"LEFT JOIN (
			SELECT st.userid,c.id,c.fullname AS 'Training_Session',scormid,name,scoid AS 'Sco_ID',title,max(attempt),element,value 
			FROM mdl_scorm_scoes_track st 
			INNER JOIN mdl_scorm s on st.scormid = s.id 
			INNER JOIN mdl_scorm_scoes AS sco on st.scoid = sco.id 
			INNER JOIN mdl_course c on s.course = c.id 
			INNER JOIN mdl_dynamic_usersgroups ug ON ug.userid = st.userid 
			INNER JOIN mdl_user u ON u.id = ug.userid 
			WHERE " . $courseSql . " 
			AND u.deleted != 1 
			AND " . $groupSql .  "
			AND element = 'cmi.core.lesson_status' GROUP BY userid,Sco_ID 
		) AS sd ON sd.userid = u.id AND u.SCO_ID = sd.Sco_ID
		WHERE sd.id is null 
		order by Last_Name,First_Name,u.Training_Session,u.Module_Name";
		
		//echo $sql;
		return $sql;
	}
	
	function buildReportByMoodleQuizQuery($groups,$courses,$orderby){
		global $datepickerto, $datepickerfrom, $resultsPerPage, $page, $paginationRequired, $rows;
		//Converts returned values from form into logical WHERE clauses for the query
		$courseSql = getWhereCourseSQL($courses,"c","id",true);
		$pfieldSql = "";
		$groupSql = getWhereGroupSQL($groups,"ug","groupid",true);
		$profileFieldsSQL = getProfileDataSQL();
		
		$selectSQL = getSelectQuizzesSQL();
		$sql = $selectSQL .
		" FROM mdl_quiz q
		INNER JOIN (
			SELECT mqa.userid, mqa.quiz,sumgrades,mqg.grade,attempt,mqg.timemodified,feedbacktext,mingrade,maxgrade
			FROM mdl_quiz_attempts AS mqa
			INNER JOIN mdl_quiz_grades mqg ON mqg.userid = mqa.userid AND mqg.quiz = mqa.quiz
			LEFT JOIN mdl_quiz_feedback AS mqf ON mqf.quizid = mqa.id
			WHERE attempt in(
				SELECT max(attempt)
				FROM mdl_quiz_attempts AS mq 
				WHERE mqa.userid = mq.userid AND mqa.quiz = mq.quiz)
			) AS qa ON q.id = qa.quiz
		INNER JOIN mdl_user u ON u.id = qa.userid
		INNER JOIN mdl_course c ON c.id = q.course 
		INNER JOIN mdl_dynamic_userdata ud ON u.id = ud.userid
		INNER JOIN (SELECT userid,groupid FROM mdl_dynamic_usersgroups ug WHERE " . $groupSql . " GROUP BY userid) AS ugroup ON u.id = ugroup.userid " .
		
		 " WHERE (qa.timemodified >= "  . getDayStart(strtotime(convertDateUkToUS($datepickerfrom))) . 
		  " and qa.timemodified <= "  . getDayEnd(strtotime(convertDateUkToUS($datepickerto))) .
		 ")"  .
		" AND " . $courseSql .
		" ORDER BY  " . $orderby . ",c.fullname,q.name" ;
		//echo $sql;
		return $sql;
	}
	
	function buildExceptionsMoodleQuizQuery($groups,$courses,$orderby){
		global $datepickerto, $datepickerfrom, $resultsPerPage, $page, $paginationRequired, $rows;
		
		//Converts returned values from form into logical WHERE clauses for the query
		$courseSql = getWhereCourseSQL($courses,"c","id",true);
		$courseSql2 = getWhereCourseSQL($courses,"e","courseid",TRUE); // new code for enrolled users
		$pfieldSql = "";
		$groupSql = getWhereGroupSQL($groups,"ug","groupid",true);
		$profileFieldsSQL = getProfileDataSQL();
		
		$selectSQL = getSelectQuizzesExceptionsSQL();
		$sql = $selectSQL .
		" FROM (
			SELECT u.id,u.username,u.firstname,u.lastname,q.fullname,q.name, Quiz_ID,q.Course_Id 
			FROM (
				SELECT * FROM mdl_user u
				INNER JOIN (SELECT userid,groupid FROM mdl_dynamic_usersgroups ug WHERE " . $groupSql  . " GROUP BY userid) ug ON u.id = ug.userid
			) AS u,
				(
				SELECT c.id AS 'Course_ID' , c.fullname, q.id AS 'Quiz_ID',q.name 
				FROM mdl_quiz q 
				INNER JOIN mdl_course c on q.course = c.id
				WHERE " . $courseSql  . "
			) AS q
		) AS u " . 
		
		// New code to only return enrolled users -----------------
		"INNER JOIN (
				SELECT userid,e.courseid
				FROM mdl_user_enrolments ue
				INNER JOIN (SELECT courseid,id FROM mdl_enrol WHERE status =0) AS e ON e.id = ue.enrolid
				WHERE " . $courseSql2 . " AND ue.status = 0 AND (ue.timestart < UNIX_TIMESTAMP() || ue.timestart = 0) AND (ue.timeend > UNIX_TIMESTAMP() || ue.timeend = 0) GROUP BY userid,courseid
		) ue ON ue.userid = u.id AND ue.courseid = u.Course_ID " .
		// --------------------------------------------------------
		
		"INNER JOIN mdl_dynamic_userdata ud ON u.id = ud.userid 
		
		LEFT JOIN ( 
			SELECT qa.userid,c.id AS 'course_id',c.fullname,q.id AS 'Quiz_ID',q.name,qa.timemodified,mqg.grade, max(attempt) AS 'attempt' 
			FROM mdl_quiz_attempts qa 
			INNER JOIN mdl_quiz q on qa.quiz = q.id 
			INNER JOIN mdl_course c on q.course = c.id 
			INNER JOIN ( 
				SELECT quiz,userid,grade FROM mdl_quiz_grades 
			) as mqg ON mqg.userid = qa.userid AND mqg.quiz = qa.quiz 
		  WHERE " . $courseSql  . "
		  GROUP BY userid,Quiz_ID  
		) AS c ON c.userid = u.id AND c.Quiz_ID = u.Quiz_ID " .
		
		"WHERE c.course_ID is null
		ORDER BY " . $orderby . ",c.fullname,c.name";	
		
		//echo $sql;
		return $sql;
	}
	
	function buildReportByHRQuery($courses,$status,$profile_field,$profiledata,$orderby){
		global $datepickerto, $datepickerfrom;
		$selectSQL = getSelectSQL();
		$courseSql = getWhereCourseSQL($courses,"c","id",true);
		$statusSql = getWhereStatusSQL($status);
		$pdataSql = getWhereProfileDataSQL($profile_field,$profiledata,true);
		
		$pfieldSql = "";
		
		$profileFieldsSQL = getProfileDataSQL(true);
		
		$sql = $selectSQL . 
		"FROM mdl_user u
		INNER JOIN mdl_scorm_scoes_track st ON u.ID = st.userid
		INNER JOIN mdl_scorm s ON st.scormid = s.id 
		INNER JOIN mdl_scorm_scoes sco ON st.scoid = sco.id 
		INNER JOIN (SELECT * FROM mdl_course c WHERE " . $courseSql . ") c ON s.course = c.id 
		INNER JOIN (SELECT * FROM mdl_dynamic_userdata ud WHERE " . $pdataSql . ") ud ON u.id = ud.userid " .
		

		"WHERE st.element = 'cmi.core.lesson_status'
		AND u.deleted != 1 
		AND u.idnumber != '' 
		AND st.id in
		 (select max(st1.id)
		  FROM mdl_scorm_scoes_track st1
		  WHERE st.userid = st1.userid
		  AND st.scormid = st1.scormid
		  AND st.scoid = st1.scoid
		  AND st1.element = 'cmi.core.lesson_status'" . 
		  $statusSql . 
		  " AND st.timemodified >= "  . getDayStart(strtotime(convertDateUkToUS($datepickerfrom))) . 
		  " AND st.timemodified <= "  . getDayEnd(strtotime(convertDateUkToUS($datepickerto))) .
		 ")" .
		" ORDER BY " . $orderby . ",c.fullname,s.name";	
		//echo $sql;
		return $sql;
	}
	
	function buildExceptionsHRQuery($courses,$profile_field,$profiledata,$orderby){
		global $datepickerto, $datepickerfrom,$reportAdditionalColumns;
		$selectSQL = getSelectExceptionsSQL();
		$courseSql = getWhereCourseSQL($courses,"c","id",TRUE);
		$courseSql2 = getWhereCourseSQL($courses,"e","courseid",TRUE); // new code for enrolled users
		$pfieldSql = "";
		$pdataSql = getWhereProfileDataSQL($profile_field,$profiledata);
		$profileFieldsSQL = getProfileDataSQL(true);
		
		$sql = $selectSQL . 
		
		"FROM (
			SELECT u.id,u.username,u.firstname,u.lastname,  " . implode(",",$reportAdditionalColumns)   . " ,s.Course_ID,s.Training_Session,s.SCORM_ID,s.Module_Name,s.SCO_ID,s.Lesson_Name FROM 

				(SELECT c.id AS 'Course_ID' , c.fullname AS 'Training_Session', scorm.id AS 'SCORM_ID', scorm.name AS 'Module_Name',scoes.id AS 'SCO_ID',scoes.title AS 'Lesson_Name' 
				FROM (SELECT * FROM mdl_scorm_scoes WHERE scormtype = 'sco') AS scoes 
				INNER JOIN mdl_scorm scorm on scorm.id = scoes.scorm 
				INNER JOIN mdl_course c on scorm.course = c.id 
				WHERE " . $courseSql . ") s,

				(SELECT id,username,firstname,lastname," . implode(",",$reportAdditionalColumns)   . "
				FROM mdl_user u
				INNER JOIN mdl_dynamic_userdata dud ON u.id = dud.userid " . 
				
				
				
				"WHERE deleted != 1  AND idnumber != '' " . $pdataSql . ") u
    		) u " . 
		// New code to only return enrolled users -----------------
		"INNER JOIN (
				SELECT userid,e.courseid
				FROM mdl_user_enrolments ue
				INNER JOIN (SELECT courseid,id FROM mdl_enrol WHERE status =0) AS e ON e.id = ue.enrolid
				WHERE " . $courseSql2 . " AND ue.status = 0 AND (ue.timestart < UNIX_TIMESTAMP() || ue.timestart = 0) AND (ue.timeend > UNIX_TIMESTAMP() || ue.timeend = 0) GROUP BY userid,courseid
		) ue ON ue.userid = u.id AND ue.courseid = u.Course_ID " .
		// --------------------------------------------------------
					
		"LEFT JOIN (
			SELECT st.userid,c.id,c.fullname AS 'Training_Session',scormid,name,scoid AS 'Sco_ID',title,max(attempt),element,value 
			FROM mdl_scorm_scoes_track st 
			INNER JOIN mdl_scorm s on st.scormid = s.id 
			INNER JOIN mdl_scorm_scoes AS sco on st.scoid = sco.id 
			INNER JOIN mdl_course c on s.course = c.id
			INNER JOIN mdl_dynamic_userdata dud ON dud.userid = st.userid
			INNER JOIN mdl_user u ON u.id = dud.userid
			WHERE " . $courseSql . " AND u.deleted != 1 ". $pdataSql . " AND
			element = 'cmi.core.lesson_status' GROUP BY userid,Sco_ID 
		) AS sd ON sd.userid = u.id AND u.SCO_ID = sd.Sco_ID
		WHERE sd.id is null 
		order by Last_Name,First_Name,u.Training_Session,u.Module_Name";
		
		//echo $sql;
		return $sql;
	}
	
	function buildReportHRQuizQuery($courses,$status,$profile_field,$profiledata,$orderby){
		global $datepickerto, $datepickerfrom;
		$selectSQL = getSelectQuizzesSQL();
		$courseSql = getWhereCourseSQL($courses);
		//$statusSql = getWhereStatusSQL($status);
		$pdataSql = getWhereProfileDataSQL($profile_field,$profiledata);
		
		$pfieldSql = "";
		
		$profileFieldsSQL = getProfileDataSQL(true); //true for HR reports
		
		$sql = $selectSQL.
		" FROM mdl_quiz q
		INNER JOIN (
			SELECT mqa.userid, mqa.quiz,sumgrades,mqg.grade,attempt,timemodified,feedbacktext,mingrade,maxgrade
			FROM mdl_quiz_attempts AS mqa
			INNER JOIN (
				SELECT quiz,userid,grade
				FROM mdl_quiz_grades
			) as mqg ON mqg.userid = mqa.userid AND mqg.quiz = mqa.quiz
			LEFT JOIN mdl_quiz_feedback AS mqf ON mqf.quizid = mqa.id
			WHERE attempt in(
				SELECT max(attempt)
				FROM mdl_quiz_attempts AS mq 
				WHERE mqa.userid = mq.userid AND mqa.quiz = mq.quiz)
			) AS qa ON q.id = qa.quiz
		INNER JOIN mdl_user u ON u.id = qa.userid
		INNER JOIN mdl_course c ON c.id = q.course" .
		
		$profileFieldsSQL . 
		
		 " WHERE (qa.timemodified >= "  . getDayStart(strtotime(convertDateUkToUS($datepickerfrom))) . 
		  " and qa.timemodified <= "  . getDayEnd(strtotime(convertDateUkToUS($datepickerto))) .
		 ")" . 
		 
		$courseSql . 
		$pdataSql . 
		" ORDER BY  " . $orderby . ",c.fullname,q.name" ;
		//echo $sql;
		return $sql;
	}
	
	function buildExceptionsHRQuizQuery($courses,$profile_field,$profiledata,$orderby){
		global $datepickerto, $datepickerfrom;
		$selectSQL = getSelectQuizzesExceptionsSQL();
		$courseSql = getWhereCourseSQL($courses,"c","id",true);
		$courseSql2 = getWhereCourseSQL($courses,"e","courseid",TRUE); // new code for enrolled users
		$pfieldSql = "";
		$pdataSql = getWhereProfileDataSQL($profile_field,$profiledata);
		$profileFieldsSQL = getProfileDataSQL(true);
		
		$sql = $selectSQL . 
		" FROM (
			SELECT u.id,u.username,u.firstname,u.lastname,q.fullname,q.name,q.Course_Id,Quiz_ID 
			FROM (
				SELECT * FROM mdl_user u
				INNER JOIN mdl_dynamic_userdata dud ON u.id = dud.userid
				WHERE deleted != 1 " . $pdataSql . "
			) AS u,
			(
				SELECT c.id AS 'Course_ID' , c.fullname, q.id AS 'Quiz_ID',q.name 
				FROM mdl_quiz q
				INNER JOIN mdl_course c on q.course = c.id
				WHERE " . $courseSql . 
			") AS q
		) AS u " . $profileFieldsSQL . 
		
		// New code to only return enrolled users -----------------
		" INNER JOIN (
				SELECT userid,e.courseid
				FROM mdl_user_enrolments ue
				INNER JOIN (SELECT courseid,id FROM mdl_enrol WHERE status =0) AS e ON e.id = ue.enrolid
				WHERE " . $courseSql2 . " AND ue.status = 0 AND (ue.timestart < UNIX_TIMESTAMP() || ue.timestart = 0) AND (ue.timeend > UNIX_TIMESTAMP() || ue.timeend = 0) GROUP BY userid,courseid
		) ue ON ue.userid = u.id AND ue.courseid = u.Course_ID " .
		// --------------------------------------------------------
		
		" LEFT JOIN (
			SELECT qa.userid,c.id AS 'course_id',c.fullname,q.id AS 'Quiz_ID',q.name,qa.timemodified,mqg.grade, max(attempt) AS 'attempt' FROM mdl_quiz_attempts qa 
			INNER JOIN mdl_quiz q on qa.quiz = q.id 
			INNER JOIN mdl_course c on q.course = c.id 
			INNER JOIN (
				SELECT quiz,userid,grade
				FROM mdl_quiz_grades
			) as mqg ON mqg.userid = qa.userid AND mqg.quiz = qa.quiz
			WHERE " . $courseSql . 
			"GROUP BY userid,Quiz_ID
		) AS c ON c.userid = u.id AND c.Quiz_ID = u.Quiz_ID " . 
		
		//This line is what makes the whole thing work by showing users with a null value for a SCO
		"WHERE c.course_ID is null
		ORDER BY " . $orderby . ",c.fullname,c.name";
		
		//echo $sql;
		return $sql;
	}
		
	function buildReportHRClassroomQuery($courses,$status,$profile_field,$profiledata,$session_status){
		global $datepickerto, $datepickerfrom,$f2fTimeAdjust;
		$selectSQL = getSelectClassroomSQL(); 
		$courseSql = getWhereCourseSQL($courses,"c","id",true);
		$statusSql = getWhereStatusSQL($status,'fss','statuscode');
		$session_status_conv = convertSessionStatus($session_status);
		$pdataSql = getWhereProfileDataSQL($profile_field,$profiledata,true);
		$pfieldSql = "";
		$profileFieldsSQL = getProfileDataSQL(true);
		
		//If the report is anything BUT the no-bookings status then do this
		if ($session_status != 4){
			$sql .= "
				SELECT 
					u.id,
					Username,
					First_Name,
					Last_Name,
					Course_Name, 
					F2F_Name, 
					F2F_Session_Id, ";
			if($session_status == 2 ){
				$sql .= "'N/A' As 'Start',
						 'N/A' AS 'Finish',
						 'N/A' AS 'Duration',";
				
			}else{
				$sql .= "FROM_UNIXTIME(Start+" . $f2fTimeAdjust . ",'%d/%m/%y <b>%h:%i</b>') AS 'Start Date/Time',
						FROM_UNIXTIME(Finish+" . $f2fTimeAdjust . ",'%d/%m/%y <b>%h:%i</b>') AS 'Finish Date/Time',
						ROUND(Duration/60,2) AS 'Duration (hrs)', ";
			}		
	
			$sql .= "'" . $session_status_conv  . "' AS 'Session Status' ,
					CASE Attendance_Status 
						WHEN '70' THEN 'Booked'
						WHEN '10' THEN 'Cancelled'
						WHEN '60' THEN 'Waitlisted'
						WHEN '80' THEN 'No Show'
						WHEN '90' THEN 'Partially Attended'
						WHEN '100' THEN 'Fully Attended'
					END AS Attendance_Status " . 
					
			$selectSQL . 	
					
			"	FROM ( 
					SELECT
						u.id,
						u.username AS 'Username',
						u.firstname AS 'First_Name',
						u.lastname AS 'Last_Name',
						fsu.userid AS 'User_ID', 
						c.id AS 'Course ID', 
						c.fullname AS 'Training Session',
						f.name AS 'F2F_Name', 
						fs.id AS 'F2F_Session_Id', 
						fs.datetimeknown AS 'DateTimeKnown',
						fs.duration AS 'Duration', 
						fsd.timestart AS 'Start', 
						fsd.timefinish AS 'Finish', 
						fss.statuscode AS 'Attendance_Status', 
						fss.superceded AS 'Superceded' " .
						$selectSQL .
						"FROM (SELECT * FROM mdl_course c WHERE " . $courseSql . ") c 
						INNER JOIN mdl_facetoface f ON c.id = f.course  
						INNER JOIN mdl_facetoface_sessions fs ON f.id = fs.facetoface 
						INNER JOIN mdl_facetoface_signups fsu ON fs.id = fsu.sessionid 
						INNER JOIN (SELECT * FROM mdl_facetoface_signups_status fss WHERE fss.superceded != 1 " . $statusSql . ") fss ON fsu.id = fss.signupid 
						INNER JOIN mdl_facetoface_sessions_dates fsd ON fs.id = fsd.sessionid 
						INNER JOIN mdl_user u ON u.id = fsu.userid
						INNER JOIN (SELECT * FROM mdl_dynamic_userdata ud WHERE " . $pdataSql . ") ud ON u.id = ud.userid
				) AS u";
				switch ($session_status){
					case 1:
						//$sql .= " WHERE Start > UNIX_TIMESTAMP() ";
						$sql .= " WHERE Start >= " . getDayStart(strtotime(convertDateUkToUS($datepickerfrom))) . " AND Start <= " . getDayEnd(strtotime(convertDateUkToUS($datepickerto))) . " ";
						break;
					case 2:
						$sql .= " WHERE DateTimeKnown = 0 ";
						break;
					case 3:
						//$sql .= " WHERE Start < UNIX_TIMESTAMP() ";
						$sql .= " WHERE Start >= " . getDayStart(strtotime(convertDateUkToUS($datepickerfrom))) . " AND Start <= " . getDayEnd(strtotime(convertDateUkToUS($datepickerto))) . " ";
				}
		
				$sql .= " ORDER BY  Last_Name,Course_Name,F2F_Name" ;
		
		//otherwise if thiis IS the No Bookings status do the following
		}else{
			$sql .= "SELECT
						u.id, 
						u.username AS 'Username', 
						u.firstname AS 'First_Name', 
						u.lastname AS 'Last_Name', 
						c.fullname AS 'Training Session',
						'' AS 'F2F_Name',
						'' AS 'F2F_Session_Id',
						'' AS 'Start Date/Time', 
						'' AS 'Finish Date/Time', 
						'' AS 'Duration (hrs)', 
						'Enrolled on course - no bookings' AS 'Session Status' , 
						'Not Booked' AS 'Attendance_Status' " . 
						$selectSQL . 
					"FROM (
						SELECT ue.userid,e.courseid " . $selectSQL  . " 
						FROM mdl_user_enrolments ue
						INNER JOIN (SELECT courseid,id FROM mdl_enrol WHERE status =0) AS e ON e.id = ue.enrolid
						INNER JOIN (SELECT * FROM mdl_dynamic_userdata ud WHERE " . $pdataSql . ") ud ON ue.userid = ud.userid #new
						WHERE (e.courseid = 12) AND ue.status = 0 AND (ue.timestart < UNIX_TIMESTAMP() || ue.timestart = 0) AND (ue.timeend > UNIX_TIMESTAMP() || ue.timeend = 0) 
						GROUP BY userid,courseid
					) ue
					LEFT JOIN (
						SELECT ud.userid,c.id AS courseid
						FROM (SELECT * FROM mdl_course c WHERE (" . $courseSql . ")) c 
						INNER JOIN mdl_facetoface f ON c.id = f.course 
						INNER JOIN mdl_facetoface_sessions fs ON f.id = fs.facetoface 
						INNER JOIN mdl_facetoface_signups fsu ON fs.id = fsu.sessionid 
						INNER JOIN mdl_user u ON u.id = fsu.userid 
						INNER JOIN (SELECT * FROM mdl_dynamic_userdata ud WHERE " . $pdataSql . ") ud ON u.id = ud.userid
					) bc ON ue.courseid = bc.courseid AND ue.userid = bc.userid
					INNER JOIN mdl_user u ON u.id = ue.userid
					INNER JOIN mdl_course c ON c.id = ue.courseid
					WHERE bc.userid IS NULL
					ORDER BY Last_Name,Course_Name";
		}
				
		//echo $sql;
		//echo "<br><br>";
		return $sql;
	}
	
	function buildReportByClassroomQuery($groups,$courses,$status,$session_status){
		global $datepickerto, $datepickerfrom,$f2fTimeAdjust;
		$selectSQL = getSelectClassroomSQL(); 
		$courseSql = getWhereCourseSQL($courses,"c","id",true);
		$statusSql = getWhereStatusSQL($status,'fss','statuscode');
		$groupSql = getWhereGroupSQL($groups,"ug","groupid",true);
		$session_status_conv = convertSessionStatus($session_status);
		
		//If the report is anything BUT the no-bookings status then do this
		if ($session_status != 4){
			$sql .= "
				SELECT 
					u.id,
					Username,
					First_Name,
					Last_Name,
					Course_Name, 
					F2F_Name, 
					F2F_Session_Id, ";
			if($session_status == 2){
				$sql .= "'N/A' As 'Start',
						 'N/A' AS 'Finish',
						 'N/A' AS 'Duration',";
				
			}else{
				$sql .= "FROM_UNIXTIME(Start+" . $f2fTimeAdjust . ",'%d/%m/%y <b>%h:%i</b>') AS 'Start Date/Time',
						FROM_UNIXTIME(Finish+" . $f2fTimeAdjust . ",'%d/%m/%y <b>%h:%i</b>') AS 'Finish Date/Time',
						ROUND(Duration/60,2) AS 'Duration (hrs)', ";
			}		
	
			$sql .= "'" . $session_status_conv  . "' AS 'Session Status' ,
					CASE Attendance_Status 
						WHEN '70' THEN 'Booked'
						WHEN '10' THEN 'Cancelled'
						WHEN '60' THEN 'Waitlisted'
						WHEN '80' THEN 'No Show'
						WHEN '90' THEN 'Partially Attended'
						WHEN '100' THEN 'Fully Attended'
					END AS Attendance_Status " . 
					
			$selectSQL . 	
					
			"	FROM ( 
					SELECT
						u.id,
						u.username AS 'Username',
						u.firstname AS 'First_Name',
						u.lastname AS 'Last_Name',
						fsu.userid AS 'User_ID', 
						c.id AS 'Course ID', 
						c.fullname AS 'Training Session',
						f.name AS 'F2F_Name', 
						fs.id AS 'F2F_Session_Id', 
						fs.datetimeknown AS 'DateTimeKnown',
						fs.duration AS 'Duration', 
						fsd.timestart AS 'Start', 
						fsd.timefinish AS 'Finish', 
						fss.statuscode AS 'Attendance_Status', 
						fss.superceded AS 'Superceded' " .
						$selectSQL .
						"FROM (SELECT * FROM mdl_course c WHERE " . $courseSql . ") c 
						INNER JOIN mdl_facetoface f ON c.id = f.course  
						INNER JOIN mdl_facetoface_sessions fs ON f.id = fs.facetoface 
						INNER JOIN mdl_facetoface_signups fsu ON fs.id = fsu.sessionid 
						INNER JOIN (SELECT * FROM mdl_facetoface_signups_status fss WHERE fss.superceded != 1 " . $statusSql . ") fss ON fsu.id = fss.signupid 
						INNER JOIN mdl_facetoface_sessions_dates fsd ON fs.id = fsd.sessionid 
						INNER JOIN mdl_user u ON u.id = fsu.userid
						INNER JOIN mdl_dynamic_userdata ud ON u.id = ud.userid
						INNER JOIN (SELECT userid,groupid FROM mdl_dynamic_usersgroups ug WHERE " . $groupSql . " GROUP BY userid) ugroup ON u.id = ugroup.userid 
				) AS u";
				switch ($session_status){
					case 1:
						//$sql .= " WHERE Start > UNIX_TIMESTAMP() ";
						$sql .= " WHERE Start >= " . getDayStart(strtotime(convertDateUkToUS($datepickerfrom))) . " AND Start <= " . getDayEnd(strtotime(convertDateUkToUS($datepickerto))) . " ";
						break;
					case 2:
						$sql .= " WHERE DateTimeKnown = 0 ";
						break;
					case 3:
						//$sql .= " WHERE Start < UNIX_TIMESTAMP() ";
						$sql .= " WHERE Start >= " . getDayStart(strtotime(convertDateUkToUS($datepickerfrom))) . " AND Start <= " . getDayEnd(strtotime(convertDateUkToUS($datepickerto))) . " ";
				}
				$sql .= " ORDER BY  Last_Name,Course_Name,F2F_Name" ;
				
		//otherwise if thiis IS the No Bookings status do the following
		}else {
			$sql .= "SELECT
				u.id, 
				u.username AS 'Username', 
				u.firstname AS 'First_Name', 
				u.lastname AS 'Last_Name', 
				c.fullname AS 'Training Session',
				'' AS 'F2F_Name',
				'' AS 'F2F_Session_Id',
				'' AS 'Start Date/Time', 
				'' AS 'Finish Date/Time', 
				'' AS 'Duration (hrs)', 
				'Enrolled on course - no bookings' AS 'Session Status' , 
				'Not Booked' AS 'Attendance_Status' " . 
				$selectSQL . 
			"FROM (
				SELECT ue.userid,e.courseid 
				FROM mdl_user_enrolments ue
				INNER JOIN (SELECT courseid,id FROM mdl_enrol WHERE status =0) AS e ON e.id = ue.enrolid " .
				
				#INNER JOIN (SELECT * FROM mdl_dynamic_userdata ud WHERE " . $pdataSql . ") ud ON ue.userid = ud.userid #new
				"INNER JOIN (SELECT userid,groupid FROM mdl_dynamic_usersgroups ug WHERE " . $groupSql . " GROUP BY userid) ugroup ON ue.userid = ugroup.userid 
				
				WHERE (e.courseid = 12) AND ue.status = 0 AND (ue.timestart < UNIX_TIMESTAMP() || ue.timestart = 0) AND (ue.timeend > UNIX_TIMESTAMP() || ue.timeend = 0) 
				GROUP BY userid,courseid
			) ue
			LEFT JOIN (
				SELECT ugroup.userid,c.id AS courseid
				FROM (SELECT * FROM mdl_course c WHERE (" . $courseSql . ")) c 
				INNER JOIN mdl_facetoface f ON c.id = f.course 
				INNER JOIN mdl_facetoface_sessions fs ON f.id = fs.facetoface 
				INNER JOIN mdl_facetoface_signups fsu ON fs.id = fsu.sessionid 
				INNER JOIN mdl_user u ON u.id = fsu.userid " . 
				
				######INNER JOIN (SELECT * FROM mdl_dynamic_userdata ud WHERE " . $pdataSql . ") ud ON u.id = ud.userid
				"INNER JOIN (SELECT userid,groupid FROM mdl_dynamic_usersgroups ug WHERE " . $groupSql . " GROUP BY userid) ugroup ON u.id = ugroup.userid 
				
			) bc ON ue.courseid = bc.courseid AND ue.userid = bc.userid
			INNER JOIN mdl_user u ON u.id = ue.userid
			INNER JOIN mdl_course c ON c.id = ue.courseid
			INNER JOIN mdl_dynamic_userdata ud ON ud.userid = ue.userid
			WHERE bc.userid IS NULL
			ORDER BY Last_Name,Course_Name";	
		
			
			
		}
			
		//echo $sql;
		//echo "<br><br>";
		return $sql;
	}
	
	/*function buildReportByCourseHRQuery($courses,$course_status,$profile_field,$profiledata,$orderby){
		global $datepickerto, $datepickerfrom;
		$selectSQL = getSelectCourseSQL();
		$course_status_conv = convertCourseStatus($course_status);		
		$courseSql = getWhereCourseSQL($courses,"c","id",true);
		$statusSql = getWhereStatusSQL($status);
		$pdataSql = getWhereProfileDataSQL($profile_field,$profiledata,true);
		
		
		$pfieldSql = "";
		$profileFieldsSQL = getProfileDataSQL(true);
		
		$sql = "
			SELECT 
				u.id,
				u.username,
				u.firstname AS 'First_Name',
				u.lastname AS 'Last_Name',
				c.fullname AS 'Training_Session',  
			    '" . $course_status_conv . "' AS 'Course_Status', 
				FROM_UNIXTIME(cc.timestarted,'%d/%m/%y')  AS 'Start_Date',
				FROM_UNIXTIME(cc.timecompleted,'%d/%m/%y') AS 'Completion_Date' " .

		$selectSQL . 
		"FROM mdl_user u

		INNER JOIN (SELECT * FROM mdl_course_completions cc
			WHERE cc.deleted IS NULL ";
			
		if($course_status == 1){
			$sql .= " AND (cc.timecompleted IS NOT NULL) " ;
			$sql .= " AND (cc.timecompleted >= " . getDayStart(strtotime(convertDateUkToUS($datepickerfrom))) . " AND cc.timecompleted <= " . getDayEnd(strtotime(convertDateUkToUS($datepickerto))) . ") ";
		}else if($course_status == 2){
			$sql .= " AND (cc.timecompleted IS NULL AND cc.timestarted != 0) " ;
			$sql .= " AND (cc.timestarted >= " . getDayStart(strtotime(convertDateUkToUS($datepickerfrom))) . " AND cc.timestarted <= " . getDayEnd(strtotime(convertDateUkToUS($datepickerto))) . ") ";
		}
		//bug fix on line below
		$sql .= ") cc ON u.id = cc.userid
		INNER JOIN (SELECT * FROM mdl_course c WHERE " . $courseSql . ") c ON cc.course = c.id 
		INNER JOIN (SELECT * FROM mdl_dynamic_userdata ud WHERE " . $pdataSql . ") ud ON u.id = ud.userid 
		
		WHERE u.deleted != 1 
		AND u.idnumber != '' 
		
		ORDER BY " . $orderby . ",c.fullname";	
		//echo $sql;
		//echo "<br><br>";
		return $sql;
	}*/
	function buildReportByCourseHRQuery($courses,$course_status,$profile_field,$profiledata,$orderby){
		global $datepickerto, $datepickerfrom,$max_rows;
		$selectSQL = getSelectCourseSQL();
		$course_status_conv = convertCourseStatus($course_status);		
		$courseSql = getWhereCourseSQL($courses,"c","id",true);
		$statusSql = getWhereStatusSQL($status);
		$pdataSql = getWhereProfileDataSQL($profile_field,$profiledata,true);
		
		
		$pfieldSql = "";
		$profileFieldsSQL = getProfileDataSQL(true);
		
		$sql = "
			SELECT 
				u.id,
				u.username,
				u.firstname AS 'First_Name',
				u.lastname AS 'Last_Name',
				c.fullname as 'Course_Name',  
			    '" . $course_status_conv . "' AS 'Course_Status', 
				FROM_UNIXTIME(cc.timestarted,'%d/%m/%y')  AS 'Start_Date',
				FROM_UNIXTIME(cc.timecompleted,'%d/%m/%y') AS 'Completion_Date' " .

		$selectSQL . 
		"FROM mdl_user u
		INNER JOIN mdl_course_completions cc ON u.id = cc.userid
		INNER JOIN mdl_course c ON cc.course = c.id
		INNER JOIN mdl_dynamic_userdata ud ON u.id = ud.userid 
		
		WHERE u.deleted != 1 
		AND u.idnumber != '' 
		AND ". $courseSql . "
		AND ". $pdataSql ;

		if($course_status == 1){
			$sql .= " AND (cc.timecompleted IS NOT NULL) " ;
			$sql .= " AND (cc.timecompleted >= " . getDayStart(strtotime(convertDateUkToUS($datepickerfrom))) . " AND cc.timecompleted <= " . getDayEnd(strtotime(convertDateUkToUS($datepickerto))) . ")";
		}else if($course_status == 2){
			$sql .= " AND (cc.timecompleted IS NULL AND cc.timestarted != 0) " ;
			$sql .= " AND (cc.timestarted >= " . getDayStart(strtotime(convertDateUkToUS($datepickerfrom))) . " AND cc.timestarted <= " . getDayEnd(strtotime(convertDateUkToUS($datepickerto))) . ")";
		}
		$sql .= " ORDER BY " . $orderby . ",c.fullname";		
		// echo "<pre>" . $sql; die;
		return $sql;
	}
	
	function buildExceptionsCourseHRQuery($courses,$profile_field,$profiledata,$orderby){
		global $datepickerto, $datepickerfrom,$reportAdditionalColumns;
		$selectSQL = getSelectCourseSQL();
		//$course_status_conv = convertCourseStatus($course_status);		
		$courseSql = getWhereCourseSQL($courses,"c","id",true);
		$courseSql2 = getWhereCourseSQL($courses,"e","courseid",true);
		$statusSql = getWhereStatusSQL($status);
		$pfieldSql = "";
		$pdataSql = getWhereProfileDataSQL($profile_field,$profiledata);
		$profileFieldsSQL = getProfileDataSQL(true);
		
		$sql = "
			SELECT 
				u.id,
				u.username,
				u.firstname AS 'First_Name',
				u.lastname AS 'Last_Name',
				c.fullname AS 'Training_Session',   
			    'Not Started' AS 'Course_Status', 
				'' AS 'Start_Date',
				'' AS 'Completion_Date' " .

		$selectSQL . 

		//NEW CODE
		" FROM mdl_course_completions cc
		INNER JOIN mdl_course c on cc.course = c.id
		INNER JOIN mdl_user u ON u.id = cc.userid
		INNER JOIN mdl_dynamic_userdata dud ON u.id = dud.userid 

		INNER JOIN mdl_user_enrolments ue ON ue.userid = cc.userid
		INNER JOIN mdl_enrol e ON e.id = ue.enrolid " . 

		"WHERE " . $courseSql . " 

		". $pdataSql . "
		AND u.deleted != 1  
		AND cc.timestarted = 0 

		AND  " . $courseSql2 . " 
		AND ue.status = 0 
		AND e.status = 0 
		AND (ue.timestart < UNIX_TIMESTAMP() || ue.timestart = 0) 
		AND (ue.timeend > UNIX_TIMESTAMP() || ue.timeend = 0)
		ORDER BY Last_Name,First_Name,Training_Session";
		//echo $sql;
		return $sql;
	}
	
	function buildReportByCourseGroupQuery($groups,$courses,$course_status,$orderby){
		global $datepickerto, $datepickerfrom;
		$selectSQL = getSelectCourseSQL();
		$course_status_conv = convertCourseStatus($course_status);		
		$courseSql = getWhereCourseSQL($courses,"c","id",true);
		$statusSql = getWhereStatusSQL($status);
		$groupSql = getWhereGroupSQL($groups,"ug","groupid",true);
		
		$sql = "
			SELECT 
				u.id,
				u.username,
				u.firstname AS 'First_Name',
				u.lastname AS 'Last_Name',
				c.fullname AS 'Training_Session',  
			    '" . $course_status_conv . "' AS 'Course_Status', 
				FROM_UNIXTIME(cc.timestarted,'%d/%m/%y')  AS 'Start_Date',
				FROM_UNIXTIME(cc.timecompleted,'%d/%m/%y') AS 'Completion_Date' " .

		$selectSQL . 
		"FROM mdl_user u

		INNER JOIN mdl_course_completions cc ON u.id = cc.userid
		INNER JOIN mdl_course c ON cc.course = c.id 
		INNER JOIN mdl_dynamic_userdata ud ON u.id = ud.userid 
		INNER JOIN mdl_dynamic_usersgroups ug ON u.id = ug.userid 
		
		WHERE u.deleted != 1 
		AND u.idnumber != ''
		AND " . $courseSql . " ";
		if($course_status == 1){
			$sql .= " AND (cc.timecompleted IS NOT NULL) " ;
			$sql .= " AND (cc.timecompleted >= " . getDayStart(strtotime(convertDateUkToUS($datepickerfrom))) . " AND cc.timecompleted <= " . getDayEnd(strtotime(convertDateUkToUS($datepickerto))) . ") ";
		}else if($course_status == 2){
			$sql .= " AND (cc.timecompleted IS NULL AND cc.timestarted != 0) " ;
			$sql .= " AND (cc.timestarted >= " . getDayStart(strtotime(convertDateUkToUS($datepickerfrom))) . " AND cc.timestarted <= " . getDayEnd(strtotime(convertDateUkToUS($datepickerto))) . ") ";
		}
		$sql .= " AND " . $groupSql .  " GROUP BY ug.userid ";
		$sql .= " ORDER BY " . $orderby . ",c.fullname";	
		// echo "<pre>" . $sql;die;
		return $sql;
	}
	
	function buildExceptionsCourseGroupQuery($groups,$courses,$orderby){
		global $datepickerto, $datepickerfrom,$reportAdditionalColumns;;
		$selectSQL = getSelectCourseSQL();
		//$course_status_conv = convertCourseStatus($course_status);		
		$courseSql = getWhereCourseSQL($courses,"c","id",true);
		$courseSql2 = getWhereCourseSQL($courses,"e","courseid",true);
		$statusSql = getWhereStatusSQL($status);
		$groupSql = getWhereGroupSQL($groups,"ugroup","groupid",true);
		
		
		$sql = "
			SELECT 
				u.id,
				u.username,
				u.firstname AS 'First_Name',
				u.lastname AS 'Last_Name',
				c.fullname AS 'Training_Session',   
			    'Not Started' AS 'Course_Status', 
				'' AS 'Start_Date',
				'' AS 'Completion_Date' " .

		$selectSQL . 

		//NEW CODE
		" FROM mdl_course_completions cc
		INNER JOIN mdl_course c on cc.course = c.id
		INNER JOIN mdl_user u ON u.id = cc.userid
		INNER JOIN mdl_dynamic_userdata dud ON u.id = dud.userid 
		INNER JOIN mdl_dynamic_usersgroups AS ugroup ON u.id = ugroup.userid 
		INNER JOIN mdl_user_enrolments ue ON ue.userid = cc.userid
		INNER JOIN mdl_enrol e ON e.id = ue.enrolid " . 

		"WHERE " . $courseSql . "
		AND " . $groupSql .  "
		AND u.deleted != 1  
		AND cc.timestarted = 0 

		AND  " . $courseSql2 . " 
		AND ue.status = 0 
		AND e.status = 0 
		AND (ue.timestart < UNIX_TIMESTAMP() || ue.timestart = 0) 
		AND (ue.timeend > UNIX_TIMESTAMP() || ue.timeend = 0)
		ORDER BY Last_Name,First_Name,Training_Session";
		//echo $sql;
		return $sql;
	}
	
	function buildReportByUser($userids){
		global $reportAdditionalColumns;
		$selectSQL = getSelectCourseSQL();
		//$userSQL = getWhereUserSQL();
		$sql = 
		"SELECT 
			u.id,
			u.username,
			u.firstname AS 'First_Name',
			u.lastname AS 'Last_Name',
			u.idnumber,
			c.fullname AS 'Training_Session', " .
			"IF(timestarted> 0,FROM_UNIXTIME(timestarted,'%d/%m/%y'),'') AS 'Start_Date'," . 
			" FROM_UNIXTIME(timecompleted,'%d/%m/%y') AS 'Completion_Date', " . 
			"IF(timestarted is null OR timestarted=0 OR timestarted='','Not Started',IF(timecompleted is null,'In Progress','Completed')) AS 'Status' " .
			$selectSQL . "  
		FROM (
			SELECT userid,e.courseid
			FROM mdl_user_enrolments ue
			INNER JOIN (SELECT courseid,id FROM mdl_enrol WHERE status =0) AS e ON e.id = ue.enrolid
			WHERE userid IN (" . $userids   . ")
			GROUP BY userid,courseid
		) ue
		LEFT JOIN (SELECT * FROM mdl_course_completions WHERE userid IN (" . $userids  . ")) cc ON ue.userid = cc.userid AND ue.courseid = cc.course
		INNER JOIN mdl_course c ON ue.courseid = c.id
		INNER JOIN mdl_dynamic_userdata dud ON ue.userid = dud.userid
		INNER JOIN mdl_user u ON u.id = dud.userid 
		ORDER BY Last_Name DESC,First_Name,Training_Session";
		//echo $sql;
		return $sql;
		
	}
	
	//Works differently as it doesn't return an sql string
	function buildReportByOverviewAcc($courses,$profile_field,$profiledata){
			global $CFG,$DB ;
			set_time_limit(0);
			$coursesSting = implode(",",$courses);
			$profiledataString = implode(",",$profiledata);
			$dataCount = count($profiledata);
			$downloadArray =  array();
			$chartArray =  array();
			array_push($chartArray, array($profile_field,'Completion Rate (%)'));
					
			$html = "<table cellpadding='0' cellspacing='0' border='0' class='display' id='styled-table'>
					<thead>
							<tr><th>" . $profile_field . "</th><th>Number of Enrolments</th><th>Number of Completions</th><th>Completion Rate (%)</th></tr>
					</thead>
					<tbody>";
			array_push($downloadArray, array($profile_field,'Number of Enrolments','Number of Completions','Completion Rate (%)')); 
			$con = mysql_connect($CFG->dbhost ,$CFG->dbuser ,$CFG->dbpassword);
			mysql_select_db($CFG->dbname, $con);
			
			for ($i=0;$i<$dataCount ;$i++){
					
					//Get the total number of enrolments
					$sql = "
					SELECT Count(*) AS 'Number_Of_Enrolments'
					FROM (
							SELECT ue.userid,e.courseid
							FROM mdl_enrol e
							INNER JOIN mdl_user_enrolments ue ON ue.enrolid = e.id
							INNER JOIN mdl_dynamic_userdata ud ON ue.userid = ud.userid
							WHERE e.status = 0 AND ue.status = 0 AND e.courseid IN (".$coursesSting.") AND
							ud." . $profile_field  . " = '" . urldecode($profiledata[$i]) .  "' 
							GROUP BY userid,courseid
					) ue";


					/*echo $sql;
					echo "<br><br>";*/
					
					$data = mysql_query($sql) or die($CFG->ErrorMessage);
					$data1 = mysql_result ($data,0);
					//echo $i;
					$html .= "<tr><td>"  . urldecode($profiledata[$i]) . "</td><td>" . $data1  . " </td>";

					//Get the total number of completions
					$sql = "
					SELECT Count(*) AS 'Number_Of_Enrolments'
					FROM (
							SELECT ue.userid,e.courseid
							FROM mdl_enrol e
							INNER JOIN mdl_user_enrolments ue ON ue.enrolid = e.id
							INNER JOIN mdl_dynamic_userdata ud ON ue.userid = ud.userid
							INNER JOIN mdl_course_completions cc ON cc.userid = ue.userid AND cc.course = e.courseid
							WHERE e.status = 0 AND ue.status = 0  AND e.courseid IN (".$coursesSting.") AND
							ud." . $profile_field  . " = '" . urldecode($profiledata[$i]) .  "' AND 
							(cc.timecompleted IS NOT NULL) 
							GROUP BY userid,courseid
					) ue";


					//echo $sql;
					//echo "<br><br>";

					$data = mysql_query($sql) or die($CFG->ErrorMessage);
					$data2 = mysql_result ($data,0);
					$html .= "<td>". $data2  . "</td>";
					$pct = round(100/$data1 * $data2);
					$html .= "<td>". $pct . "</td>";
					$html .= "</tr>";
					
					//--for download
					$tmpArr = array();
					$tmpArr[0] = urldecode($profiledata[$i]);
					$tmpArr[1] = $data1;
					$tmpArr[2] = $data2;
					$tmpArr[3] = $pct;
					array_push($downloadArray,$tmpArr);

					//for chart
					$tmpArr = array();
					$tmpArr[0] = urldecode($profiledata[$i]);
					$tmpArr[1] = $pct;
					array_push($chartArray,$tmpArr);
					
			}
			$html .= "</tbody></table>";
			mysql_close($con);
			$_SESSION['downloadArray'] = $downloadArray;
			$_SESSION['chartArray'] = $chartArray;
			return $html;
	}
	
	function buildReportByOverviewSep($courses,$profile_field,$profiledata){
			set_time_limit(0);
			global $CFG,$DB ;
			$coursesSting = implode(",",$courses);
			$profiledataString = implode(",",$profiledata);
			$dataCount = count($profiledata);
			$courseCount = count($courses);
			$downloadArray =  array();
			$con = mysql_connect($CFG->dbhost ,$CFG->dbuser ,$CFG->dbpassword);
			mysql_select_db($CFG->dbname, $con);
			//get course title
			
			//Chart Stuff ----------
			$chartArray =  array();
			$tmpArray = array();
			array_push($tmpArray, $profile_field);
			for($j=0;$j<$courseCount;$j++){
					$courses[$j];
					$data = mysql_query("SELECT fullname FROM mdl_course WHERE id = ". $courses[$j]) or die($CFG->ErrorMessage);
					$courseTitle =  mysql_result($data,0);

					$quotes = array('"' , "'");
					$courseTitle = str_replace($quotes ,"",$courseTitle);

					array_push($tmpArray,$courseTitle);
			}


			array_push($chartArray, $tmpArray);
			$tmpChartArray = array();
			//-----------------------
			
			
			$html = "<table cellpadding='0' cellspacing='0' border='0' class='display' id='styled-table'>
					<thead>
							<tr><th>" . $profile_field . "</th><th>Course</th><th>Number of Enrolments</th><th>Number of Completions</th><th>Completion Rate (%)</th></tr>
					</thead>
					<tbody>";
			array_push($downloadArray, array($profile_field,'Course','Number of Enrolments','Number of Completions','Completion Rate (%)'));        
			
			
			for ($i=0;$i<$dataCount ;$i++){
					
					$tmpChartArray = array(); //reset chart
					
					for($j=0;$j<$courseCount;$j++){
							//Get the total number of enrolments

							$sql = "
									SELECT Count(*) AS 'Number_Of_Enrolments'
									FROM (
											SELECT ue.userid,e.courseid
											FROM mdl_enrol e
											INNER JOIN mdl_user_enrolments ue ON ue.enrolid = e.id
											INNER JOIN mdl_dynamic_userdata ud ON ue.userid = ud.userid
											WHERE e.status = 0 AND ue.status = 0 AND e.courseid IN (".$courses[$j].") AND
											ud." . $profile_field  . " = '" . urldecode($profiledata[$i]) .  "' 
											GROUP BY userid,courseid
									) ue";
			
							$data = mysql_query($sql) or die($CFG->ErrorMessage);
							$data1 = mysql_result ($data,0);
							//get course title
							$data = mysql_query("SELECT fullname FROM mdl_course WHERE id = ". $courses[$j]) or die($CFG->ErrorMessage);
							$data3 = mysql_result ($data,0);

							$html .= "<tr><td>"  . urldecode($profiledata[$i]) . "</td><td>". $data3  . "</td><td>" . $data1  . " </td>";

							//Get the total number of completions

							$sql = "
									SELECT Count(*) AS 'Number_Of_Enrolments'
									FROM (
											SELECT ue.userid,e.courseid
											FROM mdl_enrol e
											INNER JOIN mdl_user_enrolments ue ON ue.enrolid = e.id
											INNER JOIN mdl_dynamic_userdata ud ON ue.userid = ud.userid
											INNER JOIN mdl_course_completions cc ON cc.userid = ue.userid AND cc.course = e.courseid
											WHERE e.status = 0 AND ue.status = 0 AND e.courseid IN (".$courses[$j].") AND
											ud." . $profile_field  . " = '" . urldecode($profiledata[$i]) .  "' AND 
											(cc.timecompleted IS NOT NULL) 
											GROUP BY userid,courseid
									) ue";


							$data = mysql_query($sql) or die($CFG->ErrorMessage);
							$data2 = mysql_result ($data,0);
							$html .= "<td>". $data2  . "</td>";
							$pct = round(100/$data1 * $data2);
							$html .= "<td>". $pct . "</td>";
							$html .= "</tr>";
							
							//--for download
							$tmpArr = array();
							$tmpArr[0] = urldecode($profiledata[$i]);
							$tmpArr[1] = $data3;
							$tmpArr[2] = $data1;
							$tmpArr[3] = $data2;
							$tmpArr[4] = $pct;
							array_push($downloadArray,$tmpArr);
							
							//for chart
							$tmpChartArray[0] = urldecode($profiledata[$i]);
							array_push($tmpChartArray,$pct);
					
					}

					array_push($chartArray,$tmpChartArray);
					
			}
			//print_r($chartArray);
			$html .= "</tbody></table>";
			mysql_close($con);
			$_SESSION['downloadArray'] = $downloadArray;
			$_SESSION['chartArray'] = $chartArray;
			return $html;
	}






	//-------------------------------------------------------------------------------------------------------------------------------------------------------------------
	// 3. Called from function in (2) to get the 'Select' statement for the query.
	//-------------------------------------------------------------------------------------------------------------------------------------------------------------------
	
	function getSelectSQL(){
		global $reportAdditionalColumns,$reportAdditionalIds,$reportStandardColumns;
		//we always need db id 
		$retVal = "SELECT u.id";
		//then look in the config file for the rest of the values
		
		if(in_array("idnumber", $reportStandardColumns)){$retVal .= ",u.idnumber AS idnumber";}
		if(in_array("username", $reportStandardColumns)){$retVal .= ",u.username";}
		if(in_array("firstname", $reportStandardColumns)){$retVal .= ",u.firstname AS 'First_Name'";}
		if(in_array("lastname", $reportStandardColumns)){$retVal .= ",u.lastname AS 'Last_Name'";}
		if(in_array("institution", $reportStandardColumns)){$retVal .= ",u.institution";}
		if(in_array("department", $reportStandardColumns)){$retVal .= ",u.department";}
		if(in_array("course name", $reportStandardColumns)){$retVal .= ",c.fullname AS 'Training Session'";}
		if(in_array("module name", $reportStandardColumns)){$retVal .= ",s.name as 'Module_Name'";}
		if(in_array("lesson name", $reportStandardColumns)){$retVal .= ",sco.title as 'Lesson_Name'";}
		if(in_array("attempt no", $reportStandardColumns)){$retVal .= ",st.attempt AS 'Attempt_No'";}
		if(in_array("status", $reportStandardColumns)){$retVal .= ", st.value AS 'Status'";}
		if(in_array("last modified", $reportStandardColumns)){$retVal .= ",FROM_UNIXTIME(st.timemodified,'%d/%m/%y') as 'Last_Accessed'";}
		if(in_array("score", $reportStandardColumns)){$retVal .= ",(select value from mdl_scorm_scoes_track st2
			 where st2.scoid = st.scoid
			 and st.attempt = st2.attempt
			 and st.scormid = st2.scormid
			 and st.userid=st2.userid
			 and st2.element = 'cmi.core.score.raw')
			as Score ";}

		// and add on the profile data info
		$count = count($reportAdditionalColumns);
		for($i=0;$i<$count;$i++){
			$retVal .= ", " . $reportAdditionalColumns[$i] . " " ;
			//$retVal .= ",ud." . $reportAdditionalColumns[$i] . " AS '" .  $reportAdditionalColumns[$i] . "' " ;
		}
		return $retVal;
	}
	function getSelectExceptionsSQL(){
		global $reportAdditionalColumns,$reportAdditionalIds,$reportStandardColumns;
		//we always need db id 
		$retVal = "SELECT u.id";
		//then look in the config file for the rest of the values
		
		if(in_array("idnumber", $reportStandardColumns)){$retVal .= ",u.idnumber AS idnumber";}
		if(in_array("username", $reportStandardColumns)){$retVal .= ",u.username";}
		if(in_array("firstname", $reportStandardColumns)){$retVal .= ",u.firstname AS 'First_Name'";}
		if(in_array("lastname", $reportStandardColumns)){$retVal .= ",u.lastname AS 'Last_Name'";}
		if(in_array("institution", $reportStandardColumns)){$retVal .= ",u.institution";}
		if(in_array("department", $reportStandardColumns)){$retVal .= ",u.department";}
		if(in_array("course name", $reportStandardColumns)){$retVal .= ",u.Training_Session AS 'Training_Session'";}
		if(in_array("module name", $reportStandardColumns)){$retVal .= ",u.Module_Name";}
		if(in_array("lesson name", $reportStandardColumns)){$retVal .= ",u.Lesson_Name";}
		
		if(in_array("attempt no", $reportStandardColumns)){$retVal .= ",'' AS 'Attempt_No'";}
		if(in_array("status", $reportStandardColumns)){$retVal .= ", 'not started' AS 'Status'";}
		if(in_array("last modified", $reportStandardColumns)){$retVal .= ",'' as 'Last_Accessed'";}
		if(in_array("score", $reportStandardColumns)){$retVal .= ",'' as Score ";}
			
			
		//The fields below are not requiredon an exception report
		// and add on the profile data info
		$count = count($reportAdditionalColumns);
		for($i=0;$i<$count;$i++){
			$retVal .= "," . $reportAdditionalColumns[$i] . " " ;
			//$retVal .= ",ud." . $reportAdditionalColumns[$i] . " AS '" .  $reportAdditionalColumns[$i] . "' " ;
		}
		return $retVal;
	};
	function getSelectQuizzesSQL(){
		global $reportAdditionalColumns,$reportAdditionalIds,$reportStandardColumns;
		//we always need db id 
		$retVal = "SELECT u.id";
		//then look in the config file for the rest of the values
		if(in_array("idnumber", $reportStandardColumns)){$retVal .= ",u.idnumber AS idnumber";}
		if(in_array("username", $reportStandardColumns)){$retVal .= ",u.username";}
		if(in_array("firstname", $reportStandardColumns)){$retVal .= ",u.firstname AS 'First_Name'";}
		if(in_array("lastname", $reportStandardColumns)){$retVal .= ",u.lastname AS 'Last_Name'";}
		if(in_array("institution", $reportStandardColumns)){$retVal .= ",u.institution";}
		if(in_array("department", $reportStandardColumns)){$retVal .= ",u.department";}
		if(in_array("course name", $reportStandardColumns)){$retVal .= ",c.fullname AS 'Training Session'";}
		if(in_array("module name", $reportStandardColumns)){$retVal .= ", q.name as 'Quiz_Name'";}
		if(in_array("module name", $reportStandardColumns)){$retVal .= ", q.id as 'Quiz_Id'";}
		if(in_array("attempt no", $reportStandardColumns)){$retVal .= ", qa.attempt AS 'Attempt_No'";}
		/*if(in_array("status", $reportStandardColumns)){$retVal .= ", 'N/A' AS 'Status'";}*/
		if(in_array("last modified", $reportStandardColumns)){$retVal .= ",FROM_UNIXTIME(qa.timemodified,'%d/%m/%y') as 'Last_Accessed'";}
		if(in_array("score", $reportStandardColumns)){$retVal .= ",CAST(qa.grade as UNSIGNED) AS 'Score'";}
		// and add on the profile data info
		$count = count($reportAdditionalColumns);
		for($i=0;$i<$count;$i++){
			$retVal .= "," . $reportAdditionalColumns[$i] . " " ;
			//$retVal .= ",ud." . $reportAdditionalColumns[$i] . " AS '" .  $reportAdditionalColumns[$i] . "' " ;
		}
		//echo $retVal;
		return $retVal;
	}
	function getSelectQuizzesExceptionsSQL(){
		global $reportAdditionalColumns,$reportAdditionalIds,$reportStandardColumns;
		//we always need db id 
		$retVal = "SELECT u.id";
		//then look in the config file for the rest of the values
		if(in_array("idnumber", $reportStandardColumns)){$retVal .= ",u.idnumber AS idnumber";}
		if(in_array("username", $reportStandardColumns)){$retVal .= ",u.username";}
		if(in_array("firstname", $reportStandardColumns)){$retVal .= ",u.firstname AS 'First_Name'";}
		if(in_array("lastname", $reportStandardColumns)){$retVal .= ",u.lastname AS 'Last_Name'";}
		if(in_array("institution", $reportStandardColumns)){$retVal .= ",u.institution";}
		if(in_array("department", $reportStandardColumns)){$retVal .= ",u.department";}
		if(in_array("course name", $reportStandardColumns)){$retVal .= ",u.fullname AS 'Training Session'";}
		if(in_array("module name", $reportStandardColumns)){$retVal .= ", u.name AS 'Quiz_Name'";}
		if(in_array("module name", $reportStandardColumns)){$retVal .= ", u.Quiz_ID AS 'Quiz_Id'";}
		if(in_array("attempt no", $reportStandardColumns)){$retVal .= ", '' AS 'Attempt_No'";}
		if(in_array("last modified", $reportStandardColumns)){$retVal .= ",'' as 'Last_Accessed'";}
		if(in_array("score", $reportStandardColumns)){$retVal .= ",'' AS 'Score'";}
		// and add on the profile data info
		$count = count($reportAdditionalColumns);
		for($i=0;$i<$count;$i++){
			$retVal .= "," . $reportAdditionalColumns[$i] . " " ;
			//$retVal .= ",ud." . $reportAdditionalColumns[$i] . " AS '" .  $reportAdditionalColumns[$i] . "' " ;
		}
		//echo $retVal;
		return $retVal;
	}
	function getSelectClassroomSQL(){
		global $reportAdditionalColumns,$reportAdditionalIds,$reportStandardColumns;
		//This is no longer as efficient as the select statements - basically main columns are fixed - this returns additional profile fields.
		$retVal = " ";
		$count = count($reportAdditionalColumns);
		for($i=0;$i<$count;$i++){
			$retVal .= "," . $reportAdditionalColumns[$i] . " " ;
			//$retVal .= ",ud." . $reportAdditionalColumns[$i] . " AS '" .  $reportAdditionalColumns[$i] . "' " ;
		}
		//echo $retVal;
		return $retVal;
	}
	function getSelectCourseSQL(){
		global $reportAdditionalColumns,$reportAdditionalIds,$reportStandardColumns;
		//Duplicated from above
		$retVal = " ";
		$count = count($reportAdditionalColumns);
		for($i=0;$i<$count;$i++){
			$retVal .= "," . $reportAdditionalColumns[$i] . " " ;
		}
		//echo $retVal;
		return $retVal;
	}

	//-------------------------------------------------------------------------------------------------------------------------------------------------------------------
	// 4. Table builders called from function in (1)
	//-------------------------------------------------------------------------------------------------------------------------------------------------------------------
	
	//TABLE BUILDER (type added on 7.6 for ind user report
	function buildHTMLTable($sql,$storeprogress=FALSE){
		global $CFG,$page, $resultsPerPage,$DB,$selfPageRef ;
		
		//Using normal php/mysql methods here because standard moodle ones don't return errors and no support for drop table
		$con = @mysql_connect($CFG->dbhost ,$CFG->dbuser ,$CFG->dbpassword);
		@mysql_select_db($CFG->dbname, $con);
		//Don't forget to close further down
		
		$data = @mysql_query($sql) or die($CFG->ErrorMessage); 
		$numFields = mysql_num_fields($data);
		
		if(@mysql_num_rows($data)!=0){
			$html = "<table cellpadding='0' cellspacing='0' border='0' class='display' id='styled-table'>
			<thead>
				<tr>";
					//Code to make datastarted appear after lastname field
					for($i=1;$i<$numFields;$i++){
						if((@mysql_field_name($data,$i) != 'datestarted')){
							$html .= "<th class='" . ucfirst(@mysql_field_name($data,$i)) ."'>" . ucfirst(mysql_field_name($data,$i)) . "</th>";
						}
						if((@mysql_field_name($data,$i) == 'Last_Name')){
							$html .= "<th class='DateStarted'>DateStarted</th>";
						}
					}
					
			$html .= "</tr>
			</thead>
			<tbody>";
			
			//For footer for Store Progress Report.
			$chk1 = 0;
			$chk2 = 0;
			$chk3 = 0;
			$chk4 = 0;

			//Populate the table with data
			while ($row = @mysql_fetch_assoc($data)) {
				$html .= "<tr>";
				foreach($row as $field=>$d){
					if( $field != 'id' && $field != 'datestarted'){
						$html .= "<td>" . $d . "</td>";
						if($field == 'Last_Name'){
							$html .= "<td>" . date("d/m/Y", $row['datestarted']) . "</td>";
						}
					}

					//Store Progress
					if ($storeprogress){
						if($field == "First Checkpoint" && $d == "No"){
							$chk1++;
						}
						if($field == "Competent" && $d == "No"){
							$chk2++;
						}
						if($field == "Experienced" && $d == "No"){
							$chk3++;
						}
						if($field == "Final" && $d == "No"){
							$chk4++;
						}
					}
				}
				$html .= "</tr>";
			} 

			$html .= "</tbody>";
			//Footer for Store Progress Report.
			if ($storeprogress){
				$html .= "
				<tfoot>
				 <tr>
				   	<th colspan =6>Totals</th>
					<th>" .$chk1 . "</th>
					<th>" .$chk2 . "</th>
					<th>" .$chk3 . "</th>
					<th>" .$chk4 . "</th>
				 </tr>
			</tfoot>
			
				";
			}

			$html .= "</table>";
		}else{
			$html = FALSE;
			
		}
		//echo "html:" . $html;
		mysql_close($con);
		return $html;
	}
	//Table builder for Quiz reports - needed a custom Feedback column.
	function buildHTMLTableQuizzes($sql){
		global $CFG,$page, $resultsPerPage,$DB,$selfPageRef ;
		//Using normal php/mysql methods here because standard moodle ones don't return errors and no support for drop table
		$con = mysql_connect($CFG->dbhost ,$CFG->dbuser ,$CFG->dbpassword);
		mysql_select_db($CFG->dbname, $con);
		//Don't forget to close further down
		
		$data = mysql_query($sql) or die($CFG->ErrorMessage); 
		$numFields = mysql_num_fields($data);
		//echo "numrows:" . mysql_num_rows($data);
		if(mysql_num_rows($data)!=0){

			$html = "<table cellpadding='0' cellspacing='0' border='0' class='display' id='styled-table'>
			<thead>
				<tr>";
				$numfields = mysql_num_fields($data);
				for($i=0;$i< $numfields;$i++){
					$field =  mysql_fetch_field($data, $i);
					//echo($field->name);
					if($field->name == "Score"){
						$html .= "<th class='Feedback'>Feedback</th>";
					}
					if ($field->name != "id" && ucfirst($field->name) != "Datestarted"  ){
						$html .= "<th class='"  . ucfirst($field->name) . "'>" . ucfirst($field->name) . "</th>";
					}
					if ($field->name == "Last_Name"){
						$html .= "<th>DateStarted</th>";
					}

				}
								
			$html .= "</tr>
			</thead>
			<tbody>";
			
			//Populate the table with data
			$countcss = 0;
			 while ($row = mysql_fetch_object($data)) {
				$html .= "<tr>";
				
				foreach($row as $key => $value) {
					if($key == "Score"){
						$html .= "<td>" . getFeedback($row->Quiz_Id, $row->Score) . "</td>";
					}
					//if ($value != 6 && $key != "id"){
					if ($key != "id" AND $key != "datestarted"){
						$html .= "<td>" . $value . "</td>";
					}
					if ($key == "Last_Name"){
						$html .= "<td>" . date("d/m/Y",$row->datestarted) . "</td>";
					}
				}
				
				$html .= "</tr>";
				$countcss++;
			} 
			
			$html .= "</table>";
		}else{
			//$html = FALSE;
			$html = FALSE;
		}
		//echo "html:" . $html;
		mysql_close($con);
		return $html;	
	}
	
	//-------------------------------------------------------------------------------------------------------------------------------------------------------------------
	// 5. Where clauses called from the query builder functions in (2)
	//-------------------------------------------------------------------------------------------------------------------------------------------------------------------
	
	
	//STATUS
	function getWhereStatusSQL($status, $alias = "st" , $field = "value", $omitAnd = false){
		$statusToSQL = " ";
		if(count($status) > 0){
			$statusToSQL .= "and (";
			$count = count($status);
			for($i=0;$i<$count;$i++){
				$statusToSQL .= $alias."." . $field ." = '" .$status[$i] . "' ";
				if ($i!=$count-1){
					$statusToSQL .= "||";
				}
			}
			$statusToSQL .= ")";
		}
		return $statusToSQL;
	}
	
	//COURSES
	function getWhereCourseSQL($courses, $alias = "c" , $field = "id", $omitAnd = false){
		$courseToSQL = " ";
		if(count($courses) > 0){
			if ($omitAnd){
				$courseToSQL .= "(";
			}else{
				$courseToSQL .= "and (";
			}
			$count = count($courses);
			for($i=0;$i<$count;$i++){
				$courseToSQL .= $alias."." . $field ." = " . $courses[$i] . " ";
				if ($i!=$count-1){
					$courseToSQL .= "||";
				}
			}
			$courseToSQL .= ")";
		}
		return $courseToSQL;
	}
	//GROUPS
	function getWhereGroupSQL($groups, $alias = "props", $field = "group_id", $omitAnd = false){
		$groupToSQL = " ";

		$groupToSQL = " ";
		if (!$omitAnd){
			$groupToSQL.= " AND ";
		}
		$groupString = implode (",",$groups);
		$groupToSQL .= $alias. "." . $field . " IN (" . $groupString . ") " ;

		return $groupToSQL;

	}
	//PROFILE DATA 
	function getWhereProfileDataSQL($field,$data,$omitAnd = false){

		$dataToSQL = " ";
		if (!$omitAnd){
			$dataToSQL.= " AND ";
		}
		
		$count = count($data);
		$dataArr = array();
		for($i=0;$i<$count;$i++){
			$val =  "\"" . urldecode($data[$i]) . "\" ";
			array_push($dataArr,$val);
		}

		$dataString = implode (",",$dataArr);
		$dataToSQL .= " " . $field . " IN (" . $dataString . ") " ;

		return $dataToSQL;
	}
	/*
		PROFILE DATA - Reads config for fields to report on and returns SQL
		Updated in 6.0 - See earlier version to see previous code. A lot of code was deleted.
		Mainly not used now - line of code is included in script.
	*/
	function getProfileDataSQL($getAll=FALSE){
		global $CFG,$DB,$reportAdditionalColumns,$reportAdditionalIds;	
		$retVal= " INNER JOIN mdl_dynamic_userdata ud ON u.id = ud.userid ";
		return $retVal;
	}
	
	
	//-------------------------------------------------------------------------------------------------------------------------------------------------------------------
	// 6. All other functions - not necessarily sql related but stored here anyway.
	//-------------------------------------------------------------------------------------------------------------------------------------------------------------------
	
	function downloadCSV($content){
		$file = 'export';
		$filename = $file."_".date("Y-m-d_H-i",time());
		header("Pragma: cache");
		header("Content-type: application/vnd.ms-excel");
		header("Content-disposition: csv" . date("Y-m-d") . ".csv");
		header( "Content-disposition: filename=".$filename.".csv");
		print $content;
	}
	function checkEmpty($str){
		if ($str == ""){
			return "&nbsp;";
		}else{
			return $str;
		}
	}
	function getDayStart($ts){
           $daystart = strtotime("midnight", $ts);
           return $daystart;
     }
     function getDayEnd($ts){
           $beginOfDay = strtotime("midnight", $ts);
           $dayend   = strtotime("tomorrow", $beginOfDay) - 1;
           return $dayend;
     }

	/*function getDayStart($ts){
		$daystart = 86400 + $ts - ($ts%86400);
		return $daystart;
	}
	function getDayEnd($ts){
		$dayend = 86400 + 86399 +($ts - ($ts%86400));
		return $dayend;
	}*/
	function convertDateUkToUS($date){
		$a = explode('/',$date);
		$date = $a[1].'/'.$a[0].'/'.$a[2]; 
		return $date;
	
	}
	
	//To get the feedback of the quiz based on the grade. COuldn't incorporate into result data so had to run as a separate function
	//Get all status data and store i an array/dataset
	function getFeedback($quizId , $grade){
		global $CFG,$DB;
		if ($grade != ''){
			$sqlStatus = "SELECT feedbacktext FROM mdl_quiz_feedback WHERE quizid = "  . $quizId. " AND mingrade<= "  .  $grade . " AND maxgrade> " . $grade ;
			$statusData = mysql_query($sqlStatus) or die($CFG->ErrorMessage);
			$feedback = mysql_fetch_row($statusData);
			return $feedback[0];
		}else{
			return "not started";
		}
	}
	
	function convertSessionStatus($ss){
		switch ($ss){
			case 1:
			return "Booking Open - Date Specific";
			
			case 2:
			return "Booking Open - Waitlisted";
			
			case 3:
			return "Session Over";
			default:
			return "Error";
		
		}
	}
	function convertCourseStatus($cs){
		switch ($cs){
			case 1:
			return "Completed";
			
			case 2:
			return "In Progress";
			
			case 3:
			return "Not Started";
			default:
			return "Error";
		}
	}
	function getSqlBasedOnStatus($ss){
		//INNER JOIN mdl_facetoface_sessions fs ON f.id = fs.facetoface
		switch ($ss){
			case 1:
			return " INNER JOIN (SELECT * FROM mdl_facetoface_sessions WHERE  fs ON f.id = fs.facetoface ";
			
			case 2:
			return "";
			
			case 3:
			return "";
			
			default:
			return "error";
		
		}
		//$retVal = INNER JOIN mdl_facetoface_sessions
	
	}
	
	
	//New for 3.0 to make manager permissions work. Will be called if user is a manager
	function storePermissions($role){
		global $DB;

		resetPermissions();
		
		$data = $DB->get_record_sql("SELECT permissions FROM mdl_dynamic_role_permissions WHERE role = ?",array($role));
		$permissions = explode("|", $data->permissions);
		$length = count($permissions);
		for($i=0;$i<$length;$i++){
			$_SESSION["permission:" . $permissions[$i]] = 1;
		}
	}
	
	function resetPermissions(){
		//all available permissions
		unset($_SESSION['permission:creategroup']);
		unset($_SESSION['permission:editgroup']);
		unset($_SESSION['permission:definegroup']);
		unset($_SESSION['permission:addcourses']);
		unset($_SESSION['permission:viewusers']);
		unset($_SESSION['permission:assignmanager']);
		unset($_SESSION['permission:deletegroup']);
		unset($_SESSION['permission:rebuildtables']);
		
		//unset($_SESSION['permission:hrreports']);
		//unset($_SESSION['permission:manualreports']);
	}
	
	//New for moodle 2.0. Catch the old depracted calls with new way of seeing if user is admin
	//Need a capabiliy that applies to techer/manager but not admin
	//
	
	/*	
		I have used a capability for finding the admin rather than finding the role by a MySQL query
		Because admins are no longer in the role_assign table and if I so go by role, then main admins
		will no longer be able to see the reporting plugins, only Site Managers. Site admins are now stored
		in config file $CFG->siteadmins see commented out function below.
	
	*/
	function isadmin(){
		//This capability is applicable to both a Site manager and an Admin. moodle/site:config
		//if (has_capability('enrol/authorize:uploadcsv', get_context_instance(CONTEXT_SYSTEM))) {
		if (has_capability('enrol/manual:config', get_context_instance(CONTEXT_SYSTEM))) {
		//if (has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) {
			return TRUE;
		}else{    
			return FALSE;
		}
	}
	
	function ismanager(){
		global $USER,$DB,$GroupManagerRoleID;
		$gids = implode(",", $GroupManagerRoleID);
		$data = $DB->get_record_sql("SELECT roleid,userid FROM mdl_role_assignments WHERE roleid IN ( " . $gids . ") AND userid = ? ",array($USER->id));
		
		if ($data){
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	//Fixes bug where php doesn't like large strings
	function echoLargeString($string, $bufferSize = 8000) {
		$splitString = str_split($string, $bufferSize);
		
		foreach($splitString as $chunk) {
			echo $chunk;
		}
	}
	
	//Replace any instances of " and ' in data.
	function replaceQuotes($data){

		$quotes = array('"' , "'");
		$data = str_replace($quotes ,"",$data);
		return $data;
		
	}
	
	//7.7 Receives array of course ids and returns course names in a comma delimit string
	function getCourseNames($courses){
		global $CFG,$page,$DB;
		$courseList = implode(",",$courses);
		$data = $DB->get_records_sql("SELECT id,fullname FROM mdl_course WHERE id IN (".$courseList.")" );	
		$cnt = count($data);
		$courseNameList = "";
		$tmp = array();
		foreach($data as $row) {
			//$encodedName = urlencode($row->data);
			//$courseNameList .= $row->fullname;
			array_push($tmp,"<b>" . $row->fullname . "</b>");
			
		}
		$courseNameList = implode(", ",$tmp);
		//echo $courseNameList;
		return $courseNameList;
		
	}
	
	/*7.7
	
	Prints something similar to the following for the Google charts API:
	
	--For Accumulative
	['jobrole','Completion Rate (%)'],
	['Checkout',25],
	['Management',0]
	
	--For Separate
	['jobrole','Microsoft Testing','Heinz Overview','Test course 1','Test course 2'],
	['Checkout',0,0,14,100],
	['Management',0,0,0,0]
	
	*/
	
	function printVisualisationData($chartArray){
		
		$numRows = count($chartArray);
		$numFields = count($chartArray[0]);
		//print_r($chartArray);
		//first row
		echo "[";
		for ($i=0;$i<$numFields;$i++){
			echo "'" . $chartArray[0][$i] . "'"; 
			if($i+1 < $numFields){
				echo ",";
			}
		}
		echo "],\n";
		
		for ($i=1;$i<$numRows;$i++){
			echo "['" .$chartArray[$i][0] . "',";
			for($j=1;$j<$numFields;$j++){
				echo "". $chartArray[$i][$j] . "";
				if($j+1 < $numFields){
					echo ",";
				}
			}
			echo "]";
			if($i+1 < $numRows){
				echo ",";
			}
			echo "\n";
		}
	}
	
	//new in 8.0
	function convertGroupTypeDisplay($val){

		return ($val == 1) ?  'Yes' :  'No';
	}
	
	function getDefinitions($groupid){
		global $CFG, $DB;
		//$row = $DB->get_record('dynamic_groupdata', array('groupid'=>$groupid));
		$result = $DB->get_records('dynamic_propertiesforgroup', array('groupid'=>$groupid),'field,data');
		$retVal = "";
		$lastfield = "";
		$count = 0;
		foreach($result as $row){
			/*if($field != 'id' && $field != 'groupid' && $value != NULL){
				$retVal .= $field . ": <b>" . $value . "</b><br>";	
			}*/
			if($count >= 5){
				$retVal .= ".....";
				break;
			}
			
			if ($lastfield != "" && $lastfield != $row->field){
				$retVal .= "<hr />";
			}
			$retVal .= $row->field . ": <b>" . $row->data . "</b><br>";	
			$lastfield = $row->field;
			$count++;
			
		}
		return $retVal;
	}
	
	function getCourseIds($groupid){
		global $CFG, $DB;
		$data = $DB->get_records('dynamic_courses_groups', array('group_id'=>$groupid),null,'course_id');
		$numItems = count($data);
		$i = 1;
		$retVal = "";
		foreach($data as $value){
			$retVal .= $value->course_id ;
			if($i != $numItems){
				$retVal .=  ", ";
			}
			$i++;

		}
		return $retVal;
		
	}
	
	function isCoach(){
		global $DB,$USER;
		$sql = "
			SELECT u.id 
			FROM mdl_user u
			INNER JOIN mdl_dynamic_userdata ud ON u.id = ud.userid
			WHERE u.id = ? AND (ud.teamcoach = 1 || ud.headteamcoach =1)
		";
		$data = $DB->get_record_sql($sql,array($USER->id));
		//var_dump($data);
		if ($data){
			return TRUE;
		}else{
			return FALSE;
		}
	}
	//'2,41876,41877,41878,41879,41880,41881,41882,41883,41886,41891,41890,41887,41920,41894,41916,170402,170401,41899,4,41915,41898,41905,170750,41911,189397,193417,193416'
	//hardcoded all site administrators
	function hasManagerCode(){
		global $DB,$USER;
		$sql = "
			SELECT u.id 
			FROM mdl_user u
			LEFT JOIN mdl_dynamic_userdata ud ON u.id = ud.userid
			WHERE u.id = ? 
			AND (ud.jobcode IN('MGRA','MGRB','MDEP','MGRC','MOPA','MSAM','AREA') OR u.id IN (41905,2,41876,41877,41878,41879,41880,41881,41882,41883,41886,41891,41890,41887,41920,41894,41916,170402,170401,41899,4,41911,41915,41898,41905,170750,41911,189397,193417,193416))
		";
		$data = $DB->get_record_sql($sql,array($USER->id));
		//var_dump($data);
		if ($data){
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	
		
?>