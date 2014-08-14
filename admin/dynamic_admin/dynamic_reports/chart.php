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
	
	if ($type = 'byOverview'){
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
	//downloadCSV($csvContent);

?>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <title>
      Google Visualization API Sample
    </title>
    <script type="text/javascript" src="http://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load('visualization', '1', {packages: ['corechart']});
    </script>
    <script type="text/javascript">
      function drawVisualization() {
        // Create and populate the data table.
        var data = google.visualization.arrayToDataTable([
          ['Year', 'Austria', 'Bulgaria', 'Denmark', 'Greece'],
          ['2003',  1336060,    400361,    1001582,   997974],
          ['2004',  1538156,    366849,    1119450,   941795],
          ['2005',  1576579,    440514,    993360,    930593],
          ['2006',  1600652,    434552,    1004163,   897127],
          ['2007',  1968113,    393032,    979198,    1080887],
          ['2008',  1901067,    517206,    916965,    1056036]
        ]);
      
        // Create and draw the visualization.
        new google.visualization.BarChart(document.getElementById('visualization')).
            draw(data,
                 {title:"Yearly Coffee Consumption by Country",
                  width:600, height:400,
                  vAxis: {title: "Year"},
                  hAxis: {title: "Cups"}}
            );
      }
      

      google.setOnLoadCallback(drawVisualization);
    </script>
  </head>
  <body style="font-family: Arial;border: 0 none;">
  HELOO
    <div id="visualization" style="width: 600px; height: 400px;"></div>
  </body>
</html>
