<?php

	if(isset($_GET['type'])){
		$type = $_GET['type'];
	}

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
		
	}else if($type == 'byHRQuiz' || $type == 'byQuiz'){
		$con = mysqli_connect($CFG->dbhost ,$CFG->dbuser ,$CFG->dbpassword);
		mysqli_select_db($con,$CFG->dbname);
			
		$sql = $_SESSION['query'];

		$csvContent = "";
		$data = mysqli_query($con,$sql) or die(mysqli_error()); 
		$numFields = mysqli_num_fields($data);
		
		while ($property = mysqli_fetch_field($data)) {
			$csvContent .= $property->name . ",";

			if(ucfirst($property->name) == "Last_Modified"){
				$csvContent .= 	"Feedback,";
			}
		}

		while ($row = mysqli_fetch_assoc($data)) {
			$csvContent .= "\n";

			foreach ($row as $key => $value){
				$csvContent .= $value;
				$csvContent .= ",";
				if($key == "Last_Modified"){
					$csvContent .= str_replace(","," ",strip_tags(getFeedback($row['Quiz_Id'], $row['Score']))) ;
					$csvContent .= ",";
				}
			}

		}
		mysqli_close($con);

	}else{
		
		$con = mysqli_connect($CFG->dbhost ,$CFG->dbuser ,$CFG->dbpassword);
		mysqli_select_db($con,$CFG->dbname);
			
		$sql = $_SESSION['query'];
		$csvContent = "";
		$data = mysqli_query($con,$sql) or die(mysqli_error()); 
		$numFields = mysqli_num_fields($data);

		while ($property = mysqli_fetch_field($data)) {
		    $csvContent .= $property->name . ",";
		}

		while ($row = mysqli_fetch_array($data)) {
			$csvContent .= "\n";
			for($i=0;$i<$numFields;$i++){
				$csvContent .= $row[$i];
				if($i != $numFields){
					$csvContent .= ",";
				}
			}
		}
		mysqli_close($con);
	}

	downloadCSV($csvContent);

?>