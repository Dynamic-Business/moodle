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
	
$subcat = $_POST['selected'];
	
$whereval = "d.data != '' AND f.shortname ='" . $subcat . "' ";

if($subcat != ""){

	$sql = "SELECT d.data AS 'data',d.id AS 'id',f.shortname AS 'shortname' FROM mdl_user_info_data d INNER JOIN mdl_user_info_field f ON f.id = d.fieldid  WHERE " . $whereval . " GROUP BY d.data";
	$rs = $DB->get_records_sql($sql);
	if(!empty($rs)){
		foreach($rs as $row) {
			$encodedName = urlencode($row->data);
			echo "<input type='checkbox' name='profiledata[]' value=" . $encodedName  .  " /><p>" . $row->data . "</p><br />" ;
		}
	}else{
		echo "<p class='no-data'>-- no data available --</p>";
	}
}else{
	//display nothing
	//disable Profile Data buttons
}











?>