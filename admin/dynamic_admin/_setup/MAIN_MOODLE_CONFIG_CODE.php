<?php  // Moodle configuration file

....

//copy the code below and replace 

require_once(dirname(__FILE__) . '/lib/setup.php');

//WITH

if(!isset($dbscript)){ //dynamic
	require_once(dirname(__FILE__) . '/lib/setup.php');
}

//This is to prevent this file from being required during the scheduled tasks, which prevents them from running.