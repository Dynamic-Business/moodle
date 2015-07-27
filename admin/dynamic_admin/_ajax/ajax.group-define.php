<?php

require_once('../config.php');
require_once('../_lib/form_lib.php');
require_once('../_lib/sql_lib.php');

//Collect Variables
$data 	= urldecode(filter_var( $_POST['data'], FILTER_SANITIZE_STRING));
$chkd 	= filter_var( $_POST['chkd'], FILTER_SANITIZE_NUMBER_INT);
$field 	= filter_var( $_POST['field'], FILTER_SANITIZE_STRING);
$groupid = filter_var( $_POST['groupid'], FILTER_SANITIZE_NUMBER_INT);
$date = time(); // get current timestamp

// == Update the Database ==
if($chkd == 1){
	//add the entry to database
	$record = new stdClass();
	$record->groupid = $groupid;
	$record->field = $field;
	$record->data = $data;
	$DB->insert_record('dynamic_propertiesforgroup', $record);

	// $sql = 'INSERT INTO mdl_dynamic_propertiesforgroup (groupid,field,data) VALUES ('. $groupid . ',"' . $field . '","' . $data . '")';

}else{
	//remove entry from database
	$record = array();
	$record['groupid'] = $groupid;
	$record['field'] = $field;
	$record['data'] = $data;

	$DB->delete_records('dynamic_propertiesforgroup', $record) ;
	$sql = 'DELETE FROM mdl_dynamic_propertiesforgroup WHERE groupid=' . $groupid .' AND field = "' . $field . '" AND data = "' . $data . '"'; 

}

// mysqli_set_charset('utf8',$con); // NO REPLACEMENT//fixes for IPF foreign characters
$DB->execute("set names 'utf8'"); // REPLACES: mysqli_query("set names 'utf8'"); //fixes for IPF foreign characters

$num = $DB->count_records('dynamic_propertiesforgroup', array( 'groupid'=>$groupid , 'field'=>$field ));

// == End of Database Update ==

//Print the result in JSON format
$data = array(
        'success' => 'true',
        'checked' => $chkd,
        'selected' => $num
        );
echo json_encode($data);

?>