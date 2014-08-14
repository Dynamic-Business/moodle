<?php
	/*
	
	- To do, code to expect array for overview report.
	
	
	*/
	if(isset($_GET['type'])){
		//$type = $_GET['type'];
		$type = filter_var( $_GET['type'], FILTER_SANITIZE_STRING); 
	}

	
	//My Library
	require_once('../config.php');
	require_once('../_lib/sql_lib.php');
	
	if ($type == 'byOverview'){
		$downloadArray = $_SESSION['downloadArray'];
		$numRows = count($downloadArray);
		$numFields = count($downloadArray[0]);
		
		for($i=0;$i<$numRows;$i++){
			for($j=0;$j<$numFields;$j++){
				$csvContent .= $downloadArray[$i][$j];
				$csvContent .= ",";
			}
			$csvContent .= "\n";
		}
		
		mysql_close($con);
		
	}else{
		
		$con = mysql_connect($CFG->dbhost ,$CFG->dbuser ,$CFG->dbpassword);
		mysql_select_db($CFG->dbname, $con);
		//Don't forget to close further down
			
		$sql = $_SESSION['query'];
		$csvContent = "";
		$data = mysql_query($sql) or die($CFG->ErrorMessage); 
		$numFields = mysql_num_fields($data);
		
		for($i=0;$i<$numFields;$i++){
			$csvContent .= 	ucfirst(mysql_field_name($data,$i)) . ",";
		}
		while ($row = mysql_fetch_array($data)) {
			$csvContent .= "\n";
			for($i=0;$i<$numFields;$i++){
				$csvContent .= $row[$i];
				if($i != $numFields){
					$csvContent .= ",";
				}
			}
		}
		mysql_close($con);
	
	}
	//echo $csvContent;
	downloadCSV($csvContent);

?>
