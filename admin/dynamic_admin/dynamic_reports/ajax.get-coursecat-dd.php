<?php

/*
	Accepts an id of the profile field selected ('selected') and returns the relevant checkbox data.
	
	Example of jQuery Call can be found in 'hr-reportscorm-builder.php'
	
	//To update Profile Data depending on what is selected in Profile Field
	 $('.data-select').change(function () {
		var val = $(this).attr("selectedIndex");
		$('.ajax-cbg').load("ajax-profile-data.php" , {selected: val});
	 });
	 
	More info can be found: http://api.jquery.com/load/
	
*/

require_once('../config.php');
require_once('../_lib/form_lib.php');
require_once('../_lib/sql_lib.php');

//$subcat = $_POST['selected'];
$id = filter_var( $_POST['selected'], FILTER_SANITIZE_STRING);
//$id = filter_var( $_GET['selected'], FILTER_SANITIZE_STRING);

//$whereval = "d.data != '' AND f.shortname ='" . $subcat . "' ";

if(isadmin() || ismanager()){

	if($id != ""){

		// $sql = "SELECT d.data AS 'data',d.id AS 'id',f.shortname AS 'shortname' FROM mdl_user_info_data d INNER JOIN mdl_user_info_field f ON f.id = d.fieldid  WHERE d.data != '' AND f.shortname = ? GROUP BY d.data";

		$sql = "
			SELECT c.id,fullname,c.idnumber
			FROM mdl_course c " . 
			"INNER JOIN mdl_course_categories cat ON c.category = cat.id " .
			"WHERE c.idnumber != '' AND enablecompletion = 1 ";
		if ($id > 0){
			//$sql .= "  AND category = " . $id . " ";
			$sql .= "  AND (cat.path LIKE '/" . $id . "'  OR cat.path LIKE '%/" . $id . "%') ";
		}
		$sql .= "  ORDER BY CAST(c.sortorder AS SIGNED),fullname" ;
		// echo $sql;
		// die;
		$rs = $DB->get_records_sql($sql);


		if(!empty($rs)){
			echo "<select name=\"course\"  class='course-status-select'>";
			foreach($rs as $row) {
				echo "<option value='" . $row->id . "'>" . $row->fullname . "</option>";
			}
			echo "</select>";
		}else{
			echo "<select name=\"course\"  class='course-status-select' disabled='true'>";
			echo "<option>No Courses</option>";
			echo "</select>";
		}
		
	}else{
		//display nothing
		//disable Profile Data buttons
	}

}else{
	echo "Illegal access - please login";
}	









?>