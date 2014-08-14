<?php
 
	/* 
	
	This is a library of functions that could be used across all plugins
	Builds from elements
	
	
	*/
	
	function buildPaginationNav(){
		global $rows,$selfPageRef,$resultsPerPage,$page;
		echo "<div id=\"pagination-nav\">";
		//echo "page:" . $page;
			if($page != 1){
				echo "<a href=\"" .  $selfPageRef  . "?page=" . ($page-1) .  "\" class=\"page-nav\">&laquo;</a>";
			}
			echo "<UL>";
			for($i=1;$i<=(ceil($rows/$resultsPerPage));$i++){
				if($page == $i){
					echo "<li>" . $i . "</a></li>";
				}else{
					echo "<li><a href=\"" . $selfPageRef  . "?page=" . $i . "\">" . $i . "</a></li>";
				}
			
			}
			echo "</ul>";
			if($page != ceil($rows/$resultsPerPage)){
				echo "<a href=\"" .  $selfPageRef  . "?page=" . ($page+1) .  "\" class=\"page-nav\">&raquo;</a>";
			}
		
		echo "</div>";
	}
	
	
	//PROFILE FIELDS: Builds Drop Down list of available Profile Fields
	// - the new $byName variable is for the id on the option values - enrolments plugin needs the name whilst the reports plguin needs the id
	function build_fields_formlist($byName = false){
	
		global $pfield,$reportAdditionalColumns,$reportAdditionalIds,$DB;
		echo "<select name=\"profile_field\" onchange=\"reload(this.form)\" class='data-select'  >";
		echo "<option> -- please select</option>";
		
		if($byName == false){
			if ($rs = $DB->get_recordset_select('user_info_field')) {
				foreach($rs as $row) {
					if($pfield == $row->id){
						echo "<option selected value='" . $row->id ."'>" . $row->name . "</option>";
					}else{
						echo "<option value='" . $row->id ."'>" . $row->name . "</option>";
					}
				}
			}
			$rs->close();
		}else if($byName == "byShortName"){
			if ($rs = $DB->get_recordset_select('user_info_field')) {
				foreach($rs as $row) {
					if($pfield == $row->name){
						echo "<option selected value='" . $row->shortname ."'>" . $row->name . "</option>";
					}else{
						echo "<option value='" . $row->shortname ."'>" . $row->name . "</option>";
					}
				}
			}
		}else if($byName == "byConfig"){
			if ($rs = $DB->get_recordset_select('user_info_field')) {
				foreach($rs as $row) {
					$exists = array_search($row->shortname,$reportAdditionalColumns);
					if(is_numeric($exists)){
						echo "<option value='" . $row->shortname ."'>" . $row->name . "</option>";
					}
				}
			}
		}else if($byName == "forDefine"){
			//reads directly from $reportAdditionalColumns
			$noOfFields = count($reportAdditionalIds);
			for ($i=0;$i<$noOfFields;$i++){
				//echo ("<option value='" . $reportAdditionalColumns[$i] . "' >" . $reportAdditionalColumns[$i] . "</option>");
				if($pfield == $reportAdditionalColumns[$i]){
					echo ("<option selected value='" . $reportAdditionalColumns[$i] . "' >" . $reportAdditionalColumns[$i] . "</option>");
				}else{
					echo ("<option value='" . $reportAdditionalColumns[$i] . "' >" . $reportAdditionalColumns[$i] . "</option>");
				}
			}	

		}else{
			if ($rs = $DB->get_recordset_select('user_info_field')) {
				foreach($rs as $row) {
					if($pfield == $row->name){
						echo "<option selected value='" . $row->name ."'>" . $row->name . "</option>";
					}else{
						echo "<option value='" . $row->name ."'>" . $row->name . "</option>";
					}
				}
			}
		}
		echo '</select>';	
	}

	//PROFILE DATA: Used to build Profile Data drop down list depending on what is selected in the field option
	function build_data_formlist($subcat = ""){
	 	global $CFG, $DB, $fielddata,$pfielddata;
		if($subcat != ""){
			$whereval = 'data != "" AND fieldid =' . $subcat;
			echo "<select name=\"profile_data\" onchange=\"reload(this.form)\" >";
			$sql = "SELECT data,id FROM " . $CFG->prefix  . "user_info_data  WHERE " . $whereval . " GROUP BY data";
			//echo "<option value='all'>All</option>";
			echo "<option readonly> -- please select</option>";
			$rs = $DB->get_records_sql($sql);
			foreach($rs as $row) {
				$encodedName = urlencode($row->data);
				if($pfielddata == $row->data){
					echo "<option selected value='" . $encodedName  ."'>" . $row->data . "</option>";
				}else{
					echo "<option value='" . $encodedName  ."'>" . $row->data . "</option>";
				}
			}
		}else{
			$whereval = 'data != ""';
			echo '<select name="profile_data" disabled>';
			echo "<option value='select'>-- please select</option>";
		}
		echo '</select>';	
	}
	
	function build_data_formlist_define($field = ""){
		global $CFG, $DB, $fielddata,$pfielddat,$groupId;
		echo "<select name=" . $field . " onchange=\"reload(this.form)\" >";
		$sql = "SELECT " . $field  . " FROM mdl_dynamic_userdata WHERE " . $field   . " IS NOT NULL AND " . $field   . " != ''  GROUP BY " . $field ;
		$currentValue = $DB->get_field('dynamic_groupdata', $field , array('groupid'=>$groupId));
		echo "<option value='NULL'>NO VALUE</option>";
		$rs = $DB->get_records_sql($sql);
		foreach($rs as $row) {
			$encodedName = urlencode($row->$field );
			if($currentValue == $row->$field ){
				echo "<option selected value='" . $encodedName  ."'>" . $row->$field . "</option>";
			}else{
				echo "<option value='" . $encodedName  ."'>" . $row->$field  . "</option>";
			}
		}
		echo '</select>';
	}
	
	function build_data_formcb($field = "",$disable = FALSE){
		global $CFG, $DB, $fielddata,$pfielddat,$groupId;
		// $sql = "SELECT d.data AS 'data',d.id AS 'id',f.shortname AS 'shortname' FROM mdl_user_info_data d INNER JOIN mdl_user_info_field f ON f.id = d.fieldid  WHERE d.data != '' AND f.shortname = ? GROUP BY d.data";
		$sql = "
		SELECT * FROM(
		SELECT d.data AS 'data',f.shortname AS 'shortname','0' as 'orph' 
		FROM mdl_user_info_data d 
		INNER JOIN mdl_user_info_field f ON f.id = d.fieldid 
		WHERE d.data != '' AND f.shortname = ?
		GROUP BY d.data
		UNION
		SELECT data,field,'1' as 'orph'
		FROM mdl_dynamic_propertiesforgroup pg
		WHERE field = ? AND groupid = " . $groupId . "
		) as d
		GROUP BY d.data";

		// echo $sql;
		// die;
		$rs = $DB->get_records_sql($sql,array($field,$field));

		$sql = "SELECT data FROM mdl_dynamic_propertiesforgroup WHERE groupid = ? AND field = ? ";
		$currentValues = $DB->get_records_sql($sql,array($groupId,$field));

		$val = array_key_exists('Checkout', $currentValues);

		$num = count($currentValues);
		echo '<div class="checkbox-group-outer">';
		echo "<div class='checkbox-group'>";
		if(!empty($rs)){
			foreach($rs as $row) {
				$checked = array_key_exists($row->data, $currentValues) ? " checked " : "" ;
				$encodedName = urlencode($row->data);
				$class = ($row->orph == 1) ? " class = 'red' " : ""; 
				$disableatt = $disable ? " disabled = 'true' " : ""; 
				echo "<input id='check' type='checkbox' name='".$field."' value=" . $encodedName  .  " " . $checked . " " . $disableatt . "   /><p " . $class . ">" . $row->data . "</p><br />" ;
			}
		}else{
			echo "<p class='no-data'>-- no data available --</p>";
		}
		echo "</div>";
		echo "<a href='#' class='expand'>Expand &#9660;</a>";
		echo "<span class='define-info'>" . $num . " selected</span>";
		echo "</div>";

	}

	//GROUPS: Build list of Group checkboxes (called diectly form page)
	function build_groups_formlist(){
	 	global $fielddata,$pfielddata;
		$sql = "";
		$groupsArray = getAllGroupsArray();
		echo "<div class='checkbox-group'>";
		for($i=0; $i<count($groupsArray); $i++){
			echo "<input type='checkbox' name='groups[]' value=" . $groupsArray[$i][0]  .  " /><p>" . $groupsArray[$i][1] . "</p><br />" ;
		}
		echo "</div>";
	}
	//COURSES - argumant of true is used for returning courses with course completion enabled
	function build_courses_formlist($completiontracking=false,$ismanager = FALSE){
		global $courseid,$status;
		$coursesArray = getAllCoursesArray($completiontracking,$ismanager); //returns multi-dim array of courses with id and course name
		echo "<div class='checkbox-group ajax-cbg2'>";
		for($i=0; $i<count($coursesArray); $i++){
			echo "<input type='checkbox' name='courses[]' value=" . $coursesArray[$i][0]  .  " /><p>" . $coursesArray[$i][1] . "</p><br />" ;
		}
		echo "</div>";
	}

	function build_category_dropdown($completiontracking=false,$storeprogress = FALSE){
		global $courseid,$status;
		$catsArray = getAllCatsArray(); //returns multi-dim array of courses with id and course name

		echo "<select name=\"cats\"  class='cat-select'>";
		if(!$storeprogress){
			echo "<option value='0'>All</option>";
		}
		for($i=0; $i<count($catsArray); $i++){
			//echo "<input type='checkbox' name='courses[]' value=" . $coursesArray[$i][0]  .  " /><p>" . $coursesArray[$i][1] . "</p><br />" ;
			if(!$storeprogress){
				echo "<option value='" . $catsArray[$i][0] . "'>" . $catsArray[$i][1] . "</option>";
			}else{
				if($catsArray[$i][1] == "Retail Academy" || $catsArray[$i][1] == "Management Academy" || $catsArray[$i][1] == "Apprentices" || $catsArray[$i][1] == "Team Talks" || $catsArray[$i][1] == "E-learning"){
					echo "<option value='" . $catsArray[$i][0] . "'>" . $catsArray[$i][1] . "</option>";
				}
			}
		}
		echo "</select>";
	}

	function build_courses_dropdown($completiontracking=false,$arr = FALSE,$ismanager = FALSE){
		global $courseid,$status;
		$coursesArray = getAllCoursesArray($completiontracking,$ismanager); //returns multi-dim array of courses with id and course name
		if($arr){
			echo "<select name=\"courses[]\"  class='course-status-select'>";

		}else{
			echo "<select name=\"course\"  class='course-status-select'>";
		}
		
		for($i=0; $i<count($coursesArray); $i++){
			//echo "<input type='checkbox' name='courses[]' value=" . $coursesArray[$i][0]  .  " /><p>" . $coursesArray[$i][1] . "</p><br />" ;
			echo "<option value='" . $coursesArray[$i][0] . "'>" . $coursesArray[$i][1] . "</option>";
		}
		echo "</select>";
	}

	//For NEXT store progress report
	function build_store_dropdown(){
		global $courseid,$status,$DB;
		$sql = "
		SELECT DISTINCT(storedetails) as 'store' FROM mdl_dynamic_userdata WHERE storedetails != ''";

		$rs = $DB->get_records_sql($sql);

		if(!empty($rs)){
			echo "<select name=\"store\"  class='store-select'>";
			foreach($rs as $row) {
				echo "<option value='" . $row->store . "'>" . $row->store. "</option>";
			}
			echo "</select>";
		}
	}


	//STATUS: Build list of status checkboxes
	function build_status_formlist(){
		global $status;
		$statusArray = getAllStatusArray();
		echo "<div class='checkbox-group-thin'>";
		for($i=0; $i<count($statusArray); $i++){
			echo "<input type='checkbox' name='status[]' value=" . $statusArray[$i]  .  " /><p>" . $statusArray[$i] . "</p><br />" ;
		}
		//Add Not Started status
		echo "</div>";
		echo "<div class='checkbox-group-thin'>";
			echo "<input type='checkbox' value='Not Started' class='not-started-cb'/><p>Not Started</p><br />" ;
		echo "</div>";
	}
	//STATUS: Course completions
	function build_status_formlist_cc(){
		echo "<select name=\"course_status\" onchange=\"reload(this.form)\" class='course-status-select'  >";
			echo "<option> -- please select</option>";
			echo "<option value=3>Not Started</option>";
			echo "<option value=2>In Progress</option>";
			echo "<option value=1>Complete</option>";
		echo '</select>';	
	}
	//STATUS: FOR QUIZZES
	function build_status_formlist_q(){
		global $status;
		echo "<div class='checkbox-group-thin' style='height:22px'>";
			echo "<input type='checkbox' name='status[]' value='Other' checked/><p>All Other Status</p><br />" ;
		echo "</div>";
		echo "<div class='checkbox-group-thin' style='height:22px'>";
			echo "<input type='checkbox' value='Not Started' class='not-started-cb'/><p>Not Started</p><br />" ;
		echo "</div>";
	}
	//F2F: Dropdown list for 
	function build_session_status_formlist(){
		
		echo "<select name=\"session_status\" onchange=\"reload(this.form)\" class='session-select'  >";
			echo "<option> -- please select</option>";
			echo "<option value=1>Booking Open - Date Specific</option>";
			echo "<option value=2>Booking Open - Waitlisted</option>";
			echo "<option value=3>Session Over</option>";
			echo "<option value=4>Enrolled on course - no bookings</option>";
		echo '</select>';	
	}
	//F2F: Checkboxes for Attendance Status
	function build_attendance_status_formlist(){
		global $courseid,$status;
		$attendanceArray = getAllAttendanceArray();
		//$coursesArray = getAllCoursesArray(); //returns multi-dim array of courses with id and course name
		echo "<div class='checkbox-group'>";
		for($i=0; $i<count($attendanceArray); $i++){
			echo "<input type='checkbox' name='status[]' value=" . $attendanceArray[$i][0]  .  " /><p>" . $attendanceArray[$i][1] . "</p><br />" ;
		}
		echo "</div>";
	}
	function build_searchusers_formlist($search){
	 	global $fielddata,$pfielddata;
		$sql = "";
		$searchUsersArray = getSearchUsersArray($search);
		echo "<div class='checkbox-group'>";
		/*for($i=0; $i<count($searchUsersArray); $i++){
			echo "<input type='checkbox' name='users[]' value=" . $searchUsersArray [$i][0]  .  " /><p>" . $searchUsersArray[$i][1] . " " . $searchUsersArray[$i][2] . " (" .$searchUsersArray[$i][3] . ")</p><br />" ;
		}*/
		echo "</div>";
	}
	//DATA ARRAYS: used by the functions above to return either normal or multidminesional arrays of data
	//STATUS: Returns normal array e.g.  array('Completed','Passed','Failed')
	function getAllStatusArray(){
		global $CFG, $DB;
		/*$tmp = array();
		$sql = "SELECT DISTINCT value FROM mdl_scorm_scoes_track WHERE element = 'cmi.core.lesson_status'";
		$rs = $DB->get_records_sql($sql);
		//print_r($rs);
		foreach($rs as $row) {
			array_push($tmp, $row->value);
		}*/
		$tmp = array('completed','incomplete','passed','failed');
		return $tmp;		
	}
	//Status of Course Completions. Just hard coded the available values unlike above. 
	/*function getAllCourseCompletionStatusArray(){
		global $CFG, $DB;
		$tmp = array('Started','Completed');
		return $tmp;		
	}*/
	//CATS: 
	function getAllCatsArray(){
		global $CFG, $DB;
		$retArr = array();
		$sql = "SELECT id,name FROM mdl_course_categories name";
		
		$rs = $DB->get_records_sql($sql);
		foreach($rs as $row) {
			$tmp = array();
			array_push($tmp , $row->id,$row->name);
			array_push($retArr,$tmp) ;
		}
		return $retArr;		
	}
	//COURSES: Returns Multi of id and course name e.g array( (1, Acme Moving and Handling) , (12, ACME Assessment) , (22, ACME Fire Safety) )
	//function getAllCoursesArray($completiontracking = false,$iscoach = FALSE){
	function getAllCoursesArray($completiontracking = false,$ismanager = FALSE){
		global $CFG, $DB;
		$retArr = array();
		$sql = "
			SELECT c.id,fullname 
			FROM mdl_course c
			INNER JOIN mdl_course_categories cat ON c.category = cat.id

			WHERE c.idnumber != '' ";
		if($completiontracking){
			$sql .= "  AND enablecompletion = 1 ";
		}
		if(!$ismanager){
			$sql .= "  AND path NOT LIKE '/36%' ";
		}

		$sql .= " ORDER BY CAST(c.sortorder AS SIGNED),fullname ";

		$rs = $DB->get_records_sql($sql);
		foreach($rs as $row) {
			$tmp = array();
			array_push($tmp , $row->id,$row->fullname);
			array_push($retArr,$tmp) ;
		}
		return $retArr;		
	}

	//GROUPS: Returns Multi of id and name e.g array( (1, Test Group) , (5, Store Managers)  )
	function getAllGroupsArray(){
		global $CFG, $DB,$USER;
		$retArr = array();
		if (isadmin()){
			//query to display all groups
			$sql = "SELECT id,name FROM mdl_dynamic_group ORDER BY name";
			
		}else if (ismanager()){
			//query to only display the groups this user is manager of
			$sql = "SELECT dg.id,name FROM mdl_dynamic_group AS dg ,mdl_dynamic_managers_group AS dmg WHERE dg.id = dmg.groupid AND dmg.userid = ? ORDER BY name";
		}
		
		$rs = $DB->get_records_sql($sql,array($USER->id));
		foreach($rs as $row) {
			$tmp = array();
			array_push($tmp , $row->id,$row->name);
			array_push($retArr,$tmp) ;
		}
		return $retArr;		
	}
	//SEARCH USERS: 
	/*This will also need to return only users in Managers group*/
	function getSearchUsersArray($search){
		global $CFG, $DB,$USER;
		//$search = 'powers';
		$retArr = array();
		if (isadmin()){
			//query to display all groups
			$sql = "SELECT u.id,firstname,lastname,idnumber FROM mdl_user u WHERE firstname = '?' OR lastname = '?' ORDER BY lastname";
			
		}else if (ismanager()){
			//query to only display the groups this user is manager of
			//$sql = "SELECT dg.id,name FROM mdl_dynamic_group AS dg ,mdl_dynamic_managers_group AS dmg WHERE dg.id = dmg.groupid AND dmg.userid = " . $USER->id . " ORDER BY name";
			
			$sql = "SELECT u.id,firstname,lastname,idnumber 
					FROM mdl_user u
					INNER JOIN (
						SELECT du.userid FROM mdl_dynamic_usersgroups du
						INNER JOIN mdl_dynamic_managers_group dmg ON du.groupid = dmg.groupid
						WHERE dmg.userid = " . $USER->id . "
					) dg ON u.id = dg.userid
					WHERE firstname = '?' OR lastname = '?' 
					ORDER BY lastname";
		}
		//echo $sql;
		$rs = $DB->get_records_sql($sql,array($search,$search));
		foreach($rs as $row) {
			$tmp = array();
			array_push($tmp , $row->u.id,$row->firstname,$row->lastname,$row->idnumber);
			array_push($retArr,$tmp) ;
		}
		return $retArr;	
	}
	
	//F2F: 
	function getAllAttendanceArray(){
	
	
	}



?>


