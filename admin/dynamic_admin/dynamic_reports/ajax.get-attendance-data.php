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

//$whereval = "d.data != '' AND f.shortname ='" . $subcat . "' ";

if($subcat != ""){

	
	switch ($subcat)
	{
	//Booking Open - Date Specific
	case 1:
		echo "<input type='checkbox' name='status[]' value=70 /><p>Booked</p><br />" ;
		echo "<input type='checkbox' name='status[]' value=10 /><p>Cancelled</p><br />" ;
		break;
	//Booking Open - Wait Listed
	case 2:
		echo "<input type='checkbox' name='status[]' value=60 /><p>Wait Listed</p><br />" ;
		echo "<input type='checkbox' name='status[]' value=10 /><p>Cancelled</p><br />" ;
		break;
	//Session Over
	case 3:
		echo "<input type='checkbox' name='status[]' value=70 /><p>Booked</p><br />" ;
		echo "<input type='checkbox' name='status[]' value=10 /><p>Cancelled</p><br />" ;
		echo "<input type='checkbox' name='status[]' value=80 /><p>No Show</p><br />" ;
		echo "<input type='checkbox' name='status[]' value=90 /><p>Partially Attended</p><br />" ;
		echo "<input type='checkbox' name='status[]' value=100 /><p>Fully Attended</p><br />" ;
		break;
	default:
	//Enrolled but no booking
	case 4:
		echo "<input type='checkbox' name='status[]' value=0 disabled='disabled' checked /><p>Not Booked</p><br />" ;
		break;
	default:
	
	break;
	} 

		
}else{
	//display nothing
	//disable Profile Data buttons
	echo "<p class='no-data'>-- no data available --</p>";
}











?>