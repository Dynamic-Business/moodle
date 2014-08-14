<?php


/*
	
	
*/


require_once('../config.php');
require_once('../_lib/form_lib.php');
require_once('../_lib/sql_lib.php');

$con = mysql_connect($CFG->dbhost,$CFG->dbuser,$CFG->dbpass);
mysql_select_db($CFG->dbname, $con);

//Collect Variables
$data 	= urldecode(filter_var( $_POST['data'], FILTER_SANITIZE_STRING));
$chkd 	= filter_var( $_POST['chkd'], FILTER_SANITIZE_NUMBER_INT);
$field 	= filter_var( $_POST['field'], FILTER_SANITIZE_STRING);
$groupid = filter_var( $_POST['groupid'], FILTER_SANITIZE_NUMBER_INT);
$date = mktime(); // get current timestamp

//debug
/*$userid = 15;
$chkd = 1;
$adminid = 2;*/



// == Update the Database ==
if($chkd == 1){
	//add the entry to database
	$sql = 'INSERT INTO mdl_dynamic_propertiesforgroup (groupid,field,data) VALUES ('. $groupid . ',"' . $field . '","' . $data . '")';

}else{
	//remove entry from database
	$sql = 'DELETE FROM mdl_dynamic_propertiesforgroup WHERE groupid=' . $groupid .' AND field = "' . $field . '" AND data = "' . $data . '"'; 

}
$result  = mysql_query($sql) or die(mysql_error());

$sqlnum = 'SELECT id FROM mdl_dynamic_propertiesforgroup WHERE groupid=' . $groupid .' AND field = "' . $field . '";';
$result = mysql_query($sqlnum) or die(mysql_error());
$num = mysql_num_rows($result);

// == End of Database Update ==

//Print the result in JSON format
$data = array(
        'success' => 'true',
        'checked' => $chkd,
        'selected' => $num
        );
echo json_encode($data);

?>