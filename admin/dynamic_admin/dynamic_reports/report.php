<?php
	//$downloadArray = $_SESSION['downloadArray'];
	//$chartArray = $_SESSION['chartArray'];
	//My Library
	require_once('../config.php');
	require_once('../_lib/form_lib.php');
	require_once('../_lib/sql_lib.php');
	
	//If this page is entered via 'index.php'(the report selection page) then POSTS and GETS will be retreived and used
	//However, if the page is called from itself, i.e. from the sort links, then it retreives its variables from the SESSION
	if(isset($_GET['type'])){
		$_SESSION['type'] = filter_var( $_GET['type'], FILTER_SANITIZE_STRING); 
		$type = filter_var( $_GET['type'], FILTER_SANITIZE_STRING); 
	}else{
		$type = $_SESSION['type'];
	}
	$noResults = FALSE;

	
	if(isset($_GET['download'])){
		$download = filter_var( $_GET['download'], FILTER_SANITIZE_STRING);
	}else{
		$download = false;
	}
	if(isset($_POST['courses'])){
		$_SESSION['courses'] = $_POST['courses'];
		$courses = $_POST['courses'];
	}else{
		if(isset($_SESSION['courses'])){
			$courses = $_SESSION['courses'];
		}else{
			$courses = array();
		}
	}
	if(isset($_POST['course'])){
		$_SESSION['course'] = $_POST['course'];
		$course = $_POST['course'];
	}else{
		if(isset($_SESSION['course'])){
			$course = $_SESSION['course'];
		}else{
			$course = "";
		}
	}

	//Array so can't sanitize
	if(isset($_POST['groups'])){
		$_SESSION['groups'] = $_POST['groups'];
		$groups = $_POST['groups'];
	}else{
		if(isset($_SESSION['groups'])){
			$groups = $_SESSION['groups'];
		}else{
			$groups = array();
		}
	}

	//Array so can't sanitize
	if(isset($_POST['status'])){
		$_SESSION['status'] = $_POST['status'];
		$status = $_POST['status'];
	}else{
		if(isset($_SESSION['status'])){
			$status = $_SESSION['status'];
		}else{
			$status = array();
		}
	}
	//Used for HR reports
	if(isset($_POST['profile_field'])){
		$_SESSION['profile_field'] = filter_var( $_POST['profile_field'], FILTER_SANITIZE_STRING);
		$profile_field = filter_var( $_POST['profile_field'], FILTER_SANITIZE_STRING);
	}else{
		if(isset($_SESSION['profile_field'])){
			$profile_field = $_SESSION['profile_field'];
		}else{
			$profile_field = "";
		}
	}
	//Array
	if(isset($_POST['profiledata'])){
		$_SESSION['profiledata'] = $_POST['profiledata'];
		$profiledata = $_POST['profiledata'];
	}else{
		if(isset($_SESSION['profiledata'])){
			$profiledata = $_SESSION['profiledata'];
		}else{
			$profiledata = array();
		}
	}
	
	//For Session status in F2F
	if(isset($_POST['session_status'])){
		$_SESSION['session_status'] = $_POST['session_status'];
		$session_status = $_POST['session_status'];

	}else{
		if(isset($_SESSION['session_status'])){
			$session_status = $_SESSION['session_status'];
		}else{
			$session_status = array();
		}
	}
	//For Session status in course reports
	if(isset($_POST['course_status'])){
		$_SESSION['course_status'] = $_POST['course_status'];
		$course_status = $_POST['course_status'];
	}else{
		if(isset($_SESSION['course_status'])){
			$course_status = $_SESSION['course_status'];
		}else{
			$course_status = 0;
		}
	}
	
	//For individual Report - Array
	if(isset($_POST['userids'])){
		$_SESSION['userids'] = $_POST['userids'];
		$userids = $_POST['userids'];
	}else{
		if(isset($_SESSION['userids'])){
			$userids = $_SESSION['userids'];
		}else{
			$userids = array();
		}
	}

	//If call came from Broswe user page inside Moodle
	if(isset($_GET['userids'])){
		//$userids = $_GET['userids'];
		//echo "before:" . $_GET['userids'];
		$userids = filter_var($_GET['userids'], FILTER_SANITIZE_NUMBER_INT); 
		//echo "after:" . $userids;

	}
	
	if(isset($_POST['overviewtype'])){
		// $_SESSION['overviewtype'] = $_POST['overviewtype'];
		// $overviewtype = $_POST['overviewtype'];

		$_SESSION['overviewtype'] = filter_var( $_POST['overviewtype'], FILTER_SANITIZE_STRING);
		$overviewtype = filter_var( $_POST['overviewtype'], FILTER_SANITIZE_STRING);

	}else{
		if(isset($_SESSION['overview-type'])){
			$overviewtype = $_SESSION['overviewtype'];
		}else{
			$overviewtype = array();
		}
	}
	
	//For login type on user logins report
	if(isset($_POST['logintype'])){
		$_SESSION['logintype'] = filter_var( $_POST['logintype'], FILTER_SANITIZE_STRING);
		$logintype = filter_var( $_POST['logintype'], FILTER_SANITIZE_STRING);
	}else{
		if(isset($_SESSION['logintype'])){
			$logintype = $_SESSION['logintype'];
			$logintype = filter_var( $_POST['logintype'], FILTER_SANITIZE_STRING);
		}else{
			$logintype = array();
		}
	}

	if(isset($_POST['store'])){
		$_SESSION['store'] = filter_var( $_POST['store'], FILTER_SANITIZE_STRING);
		$store = filter_var( $_POST['store'], FILTER_SANITIZE_STRING);
	}else{
		if(isset($_SESSION['store'])){
			$store = $_SESSION['store'];
			$store= filter_var( $_POST['store'], FILTER_SANITIZE_STRING);
		}else{
			$store = array();
		}
	}


	//DATES --------
	if(isset($_POST['datepickerto'])){
		$_SESSION['datepickerto'] = filter_var( $_POST['datepickerto'], FILTER_SANITIZE_STRING);
		$datepickerto = filter_var( $_POST['datepickerto'], FILTER_SANITIZE_STRING);
	}else{
		if(isset($_SESSION['datepickerto'])){
			$datepickerto = $_SESSION['datepickerto'];
		}else{
			$datepickerto = "";
		}
	}
	if(isset($_POST['datepickerfrom'])){
		$_SESSION['datepickerfrom'] = filter_var( $_POST['datepickertofrom'], FILTER_SANITIZE_STRING);
		$datepickerfrom = filter_var( $_POST['datepickerfrom'], FILTER_SANITIZE_STRING);
	}else{
		if(isset($_SESSION['datepickerfrom'])){
			$datepickerfrom = $_SESSION['datepickerfrom'];
		}else{
			$datepickerfrom = "";
		}
	}
	//--------------
	if(isset($_SESSION['htmlReportTable'])){
			$htmlReportTable = $_SESSION['htmlReportTable'];
		}else{
			$htmlReportTable = "";
		}

	
	if(isset($_SESSION['csvContent'])){
			$csvContent = $_SESSION['csvContent'];
		}else{
			$csvContent = "";
		}
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Moodle Reports</title>
	<?php echo $OUTPUT->standard_head_html(); ?>
    <link href="../_css/styles.css" rel="stylesheet" type="text/css" />
	<style type="text/css" title="currentStyle">
			@import "../_datatables/media/css/demo_page.css";
			@import "../_datatables/media/css/demo_table_jui.css";
			@import "../_datatables/themes/smoothness/jquery-ui-1.8.4.custom.css";
	</style>
	<?php if (isadmin()){ ?>
	<script type="text/javascript" language="javascript" src="../_datatables/media/js/jquery.js"></script>
	<script type="text/javascript" language="javascript" src="../_datatables/media/js/jquery.dataTables.js"></script>
	<script type="text/javascript" charset="utf-8">
		$(document).ready(function() {
			var oTable = $('#styled-table').dataTable({
				"bJQueryUI": true,
				"sPaginationType": "full_numbers",
				"bRetrieve": true,
				"sScrollX": "100%",
				/*Add more columns to hide here*/
				"aoColumnDefs": [{ "bVisible": false, "aTargets": ["Headteamcoach","Teamcoach","Idnumber","Username","Homedesignconsultant","Stylist",
					"Tailoring",
					"Shoe",
					"Firstaider",
					"Appointed",
					"Vandriver",
					"Area",
					"Quiz_Id",
					"Feedback",
					"Homespecialist",
					"Areavm",
					"Banksman",
					"Cia",
					"Lingerie"] }]
			});
			
		
		} );		
	</script>
    <?php }else if (ismanager()){ ?>
	<script type="text/javascript" language="javascript" src="../_datatables/media/js/jquery.js"></script>
	<script type="text/javascript" language="javascript" src="../_datatables/media/js/jquery.dataTables.js"></script>
	<script type="text/javascript" charset="utf-8">
		$(document).ready(function() {
			var oTable = $('#styled-table').dataTable({
				"bJQueryUI": true,
				"sPaginationType": "full_numbers",
				"bRetrieve": true,
				"sScrollX": "100%",
				/*Add more columns to hide here*/
				"aoColumnDefs": [{ "bVisible": false, "aTargets": ["Headteamcoach","Teamcoach","Idnumber","Username","Homedesignconsultant","Stylist",
					"Tailoring",
					"Shoe",
					"Firstaider",
					"Vandriver",
					"Area",
					"Quiz_Id",
					"Feedback",
					"Homespecialist",
					"Areavm",
					"Banksman",
					"Cia",
					"Lingerie"] }]
			});
			
			
		
		} );		
	</script>
	<?php } ?>
	<script type="text/javascript">
		$(document).ready(function()
		{
		    $("#loading").hide();
		});
	</script>
	
    <!-- CODE FOR CHART VIEW -->
    <?php if ($type == 'byOverview'){ ?>
        <!-- Fancy box and google api stuff: help found on here: http://stackoverflow.com/questions/7851576/show-google-visualization-api-within-fancybox-js-pop-up -->
        <script type="text/javascript" src="../_fancybox/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
        <link rel="stylesheet" href="../_fancybox/fancybox/jquery.fancybox-1.3.4.css" type="text/css" media="screen" />
        <!--<script type="text/javascript" src="http://www.google.com/jsapi"></script>
        <script type="text/javascript"> google.load('visualization', '1', {packages: ['corechart']});</script>
        <script>
        	function drawVisualization() {
				var data = google.visualization.arrayToDataTable([
					
					<?php //printVisualisationData($chartArray); ?>
					
				  ]);
				  var options = {title: 'Overview Chart', vAxis: {title: '<?php echo $chartArray[0][0] ?> '}, hAxis: {title: 'Percentage (%)',maxValue : 100,minValue: 0}};

				new google.visualization.BarChart(document.getElementById('data')).draw(data,options);
        	}
        </script>-->
        <script type="text/javascript" charset="utf-8">
            $(document).ready(function() {
                /* This is basic - uses default settings */
              $(".fbpopup").fancybox({'hideOnContentClick': true,onComplete: drawVisualization});
              $("#btn").click(function(){
                $(".fbpopup").trigger('click');
              });
            } );
        </script>
	<?php } ?>
    
    <!-- -->
    
    
</head>
<body class='plugins'>
	<!-- Loading Image -->
	<div id="loading"><img  alt="" src="../_img/ajax-loader.gif"/><br><p>Loading report. This may take a while. Please do not refresh your browser.</p></div>
	<?php 
		ob_flush();
		flush(); 
	
	switch ($type)
		{
		case "byGroup":
			$title = "Report: E-Learning Trackers";
		  	$htmlReportTable = reportByGroup($groups,$courses,$status);
			if (!$htmlReportTable){
				$htmlReportTable = "<p class='no-results'>Your report returned no results</p>";
				$noResults = TRUE;
			}
			
			$linkBack = "<p><a href='". $CFG->wwwroot . "'>Moodle</a> &raquo; <a href='" .  $linkBackToReports . "'>Reports</a> &raquo; <a href='" . $linkBackToScRepBuilder . "'>E-Learning Trackers</a> &raquo; Report</p>";
			
		  break;
		  
		case "byQuiz":
			$title = "Report: Quiz Results";
		  	$htmlReportTable = reportByMoodleQuiz($groups,$courses,$status);
			if (!$htmlReportTable){
				$htmlReportTable = "<p class='no-results'>Your report returned no results</p>";
				$noResults = TRUE;
			}
			$linkBack = "<p><a href='". $CFG->wwwroot . "'>Moodle</a> &raquo; <a href='" .  $linkBackToReports . "'>Reports</a> &raquo; <a href='" . $linkBackToQRepBuilder . "'>Quiz Results</a> &raquo; Report</p>";
		  break;
		  
		case "byHR":
			$title = "Report: HR Profile Data Report (SCORM)";
		  	$htmlReportTable = reportByHR($courses,$status,$profile_field,$profiledata);
			if (!$htmlReportTable){
				$htmlReportTable = "<p class='no-results'>Your report returned no results</p>";
				$noResults = TRUE;
			}
			
			$linkBack = "<p><a href='". $CFG->wwwroot . "'>Moodle</a> &raquo; <a href='" .  $linkBackToReports . "'>Reports</a> &raquo; <a href='" . $linkBackToScHrRepBuilder . "'>HR Profile Data Report (Scorm Courses)</a> &raquo; Report</p>";
			
		  break;
		  
		case "byHRQuiz":
			$title = "Report: HR Profile Data Report (Moodle Quizzes)";
		  	$htmlReportTable = reportByHRQuiz($courses,$status,$profile_field,$profiledata);
			if (!$htmlReportTable){
				$htmlReportTable = "<p class='no-results'>Your report returned no results</p>";
				$noResults = TRUE;
			}
			
			$linkBack = "<p><a href='". $CFG->wwwroot . "'>Moodle</a> &raquo; <a href='" .  $linkBackToReports . "'>Reports</a> &raquo; <a href='" . $linkBackToQHrRepBuilder . "'>HR Profile Data Report (Moodle Quizzes)</a> &raquo; Report</p>";
			
		 break;
		    
		case "viewManagers":
			$title = "Report: View Group Managers";
		  	$htmlReportTable = reportViewGroupManagers();
			if (!$htmlReportTable){
				$htmlReportTable = "<p class='no-results'>Your report returned no results</p>";
				$noResults = TRUE;
			}
			$linkBack = "<p><a href='". $CFG->wwwroot . "'>Moodle</a> &raquo; <a href='" . $linkBackToReports . "'>Reports</a> &raquo; View Group Managers</p>";
		  break;
		
		// New Face to Face Reports --------------  
		case "byHRClassroom":
			$title = "Report: HR Profile Data Report (Classroom Sessions)";
		  	$htmlReportTable = reportByHRClassroom($courses,$status,$profile_field,$profiledata,$session_status);
			if (!$htmlReportTable){
				$htmlReportTable = "<p class='no-results'>Your report returned no results</p>";
				$noResults = TRUE;
			}
			$linkBack = "<p><a href='". $CFG->wwwroot . "'>Moodle</a> &raquo; <a href='" . $linkBackToReports . "'>Reports</a> &raquo; <a href='" . $linkBackToClassroomHrRepBuilder . "'>HR Profile Data Report (Classroom Sessions)</a> &raquo; Report</p>";
		  break;
		
		case "byClassroom":
			$title = "Report: Manual Group Report (Classroom Sessions)";
		  	$htmlReportTable = reportByClassroom($groups,$courses,$status,$session_status);
			if (!$htmlReportTable){
				$htmlReportTable = "<p class='no-results'>Your report returned no results</p>";
				$noResults = TRUE;
			}
			$linkBack = "<p><a href='". $CFG->wwwroot . "'>Moodle</a> &raquo; <a href='" . $linkBackToReports . "'>Reports</a> &raquo; <a href='" . $linkBackToClassroomRepBuilder . "'>Manual Group Report (Classroom Sessions)</a> &raquo; Report</p>";
		  break;
		 
		 //New Course Status Reports from 7.3
		 case "byCourseHR":
			$title = "Report: HR Profile Data Report (Course Completions)";
		  	$htmlReportTable = reportByCourseHR($courses,$course_status,$profile_field,$profiledata);
			if (!$htmlReportTable){
				$htmlReportTable = "<p class='no-results'>Your report returned no results</p>";
				$noResults = TRUE;
			}
			
			$linkBack = "<p><a href='". $CFG->wwwroot . "'>Moodle</a> &raquo; <a href='" .  $linkBackToReports . "'>Reports</a> &raquo; <a href='" . $linkBackToCourseHrRepBuilder . "'>HR Profile Data Report (Course Completions)</a> &raquo; Report</p>";
			
		 break;
		 
		 case "byCourse":
			$title = "Report: Store Training Records";
		  	$htmlReportTable = reportByCourse($groups,$courses,$course_status);
			if (!$htmlReportTable){
				$htmlReportTable = "<p class='no-results'>Your report returned no results</p>";
				$noResults = TRUE;
			}
			
			$linkBack = "<p><a href='". $CFG->wwwroot . "'>Moodle</a> &raquo; <a href='" .  $linkBackToReports . "'>Reports</a> &raquo; <a href='" . $linkBackToCourseRepBuilder . "'>Store Training Records</a> &raquo; Report</p>";
			
		 break;
		 
		 case "byUser":
			$title = "Report: Individual User Report";
		  	$htmlReportTable = reportByUser($userids);
			if (!$htmlReportTable){
				$htmlReportTable = "<p class='no-results'>Your report returned no results</p>";
				$noResults = TRUE;
			}
			
			$linkBack = "<p><a href='". $CFG->wwwroot . "'>Moodle</a> &raquo; <a href='" .  $linkBackToReports . "'>Reports</a> &raquo; <a href='" . $linkBackToUserRepBuilder . "'>Individual Learning Report</a> &raquo; Report</p>";
			
		 break;
		 
		 case "byOverview":
		 //echo $overviewtype;
			$title = "Report: Overview Report";
		  	$htmlReportTable = reportByOverview($courses,$profile_field,$profiledata,$overviewtype);
			if (!$htmlReportTable){
				$htmlReportTable = "<p class='no-results'>Your report returned no results</p>";
				$noResults = TRUE;
			}
			
			$linkBack = "<p><a href='". $CFG->wwwroot . "'>Moodle</a> &raquo; <a href='" .  $linkBackToReports . "'>Reports</a> &raquo; <a href='" . $linkBackToOverviewRepBuilder . "'>Progress Report</a> &raquo; Report</p>";
			$courseList = getCourseNames($courses);
			$reportInfo = "<p class='rep-info'>You selected the following course(s): " . $courseList . "</p>";
		 break;

		 case "byLogins":
			$title = "Report: Overview Report";
		  	$htmlReportTable = reportByLogins($logintype);
			if (!$htmlReportTable){
				$htmlReportTable = "<p class='no-results'>Your report returned no results</p>";
				$noResults = TRUE;
			}
			
			$linkBack = "<p><a href='". $CFG->wwwroot . "'>Moodle</a> &raquo; <a href='" .  $linkBackToReports . "'>Reports</a> &raquo; <a href='" . $linkBackToLoginRepBuilder . "'>User Login Report</a> &raquo; Report</p>";
			if($logintype == 'logins'){
				$reportInfo = "<p class='rep-info'>Report shows all user logins between <b>$datepickerfrom</b> to <b>$datepickerto</b>.</p>";
			}else{
				$reportInfo = "<p class='rep-info'>Showing all users who have never logged into the website.</p>";
			}
		 break;

		 case "byStoreProgress":
			$title = "Report: Exception Report";
		  	$htmlReportTable = reportByStoreProgress($groups,$course);
		  	//var_dump($groups);die;

			if (!$htmlReportTable){
				$htmlReportTable = "<p class='no-results'>Your report returned no results</p>";
				$noResults = TRUE;
			}
			$courseList = getCourseNames(explode(",",$course)); //convert to array so can use this function
			
			$linkBack = "<p><a href='". $CFG->wwwroot . "'>Moodle</a> &raquo; <a href='" .  $linkBackToReports . "'>Reports</a> &raquo; <a href='" . $linkBackToStoreProgressBuilder . "'>Exception Report</a> &raquo; Report</p>";
			$reportInfo = "<p class='rep-info'>Report shows exceptions for <b>" .$courseList . "</b>.</p>";
			$reportInfo2 = "<p class='rep-info' style='color:#f9023c;float;left;clear:none;'>Staff that are behind with their training are shown on this report. There are four 'checkpoints' that must be ticked off at certain times on the individual's training programme, depending on when their training started. Once training is back on track and the checkpoint has been ticked, they will no longer show on the report.</p>";
		 break;
		 
		 
		  
		default:
		  //all user data
		} 
	
	if($download){
		downloadCSV($csvContent);
	}
	$downloadArray = $_SESSION['downloadArray'];
	$chartArray = $_SESSION['chartArray'];

?>


	<?php include "../_inc/header.php"; ?>
    <h2 class="report-header"><?php echo $title; ?> </h2>
    <div class="links">
        <?php echo $linkBack; ?> 
    </div>

<?php

	if (isadmin() || ismanager()) { 
			if(isset($reportInfo)){ echo $reportInfo; }
			
			 if(!$noResults){?>

            
                <div id="admin-buttons"  >

                     <button class='but'  onClick="window.location='download.php?type=<?php echo $type; ?>'"  style='<?php if(isset($reportInfo2)){ echo "float:left;margin-right:10px"; } ?>'>Download to Excel</button>
                     
                     <?php  if ($type == 'byOverview'){ ?>
						 	
                         	 <!-- can't load fancybox directly from a button so hidden A is used to do the trick-->
                             <!--<button id='btn' class='but' >View Chart</button>-->
                             <a href="#data" class='fbpopup' style='display:none'>Click</a>
                             
                             <!--<a id="inline" href="#data">Click here to see chart</a>-->
                             <div style="display:none"><div style='height:800px;width:800px' id="data"></div></div>



                         		
					 <?php } ?>
					 <?php if(isset($reportInfo2)){ echo $reportInfo2; } ?>
                      
                </div>

                
			
			<?php } 
			
			echoLargeString($htmlReportTable);

	 }else{ ?>
		<p class="no-permission">You do not have the correct permissions to view this page</p>
<?php	} ?> 

<?php
	
	echo $footer;
	
?>
</body>
</html>
