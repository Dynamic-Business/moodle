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
	

//$searchtext = $_POST['searchtext'];
$searchtext = filter_var( $_POST['searchtext'], FILTER_SANITIZE_STRING);



//$searchtext = $_GET['searchtext'];
	
$whereval = "(firstname = '" . $searchtext . "' OR lastname = '" . $searchtext . "') ";

if($searchtext != ""){
	//
	if (isadmin()){
		$sql = "SELECT id,firstname,lastname,idnumber FROM mdl_user WHERE " . $whereval . " AND deleted != 1 ORDER BY lastname " ;
	}else if (ismanager()){

		$sql = "SELECT u.id,firstname,lastname,idnumber FROM mdl_dynamic_usersgroups ug
				INNER JOIN (SELECT userid,groupid FROM mdl_dynamic_managers_group WHERE userid = " . $USER->id . " ) mg ON ug.groupid = mg.groupid 
				INNER JOIN mdl_user u ON ug.userid = u.id WHERE " . $whereval . " AND u.deleted != 1 ORDER BY lastname " ; 
	}
	//else if group manager
	
	
	$rs = $DB->get_records_sql($sql);
	if(!empty($rs)){
		foreach($rs as $row) {
			//$encodedName = urlencode($row->data);
			
			echo "<input type='checkbox' name='users[]' value=" . $row->id  .  " class='users' /><p>" . $row->firstname . " " . $row->lastname  . " (" . $row->idnumber . ") </p><br />" ;
		}
	}else{
		echo "<p class='no-data'>-- No results matched your search --</p>";
	}
}else{
	//display nothing
	//disable Profile Data buttons
	
}













?>