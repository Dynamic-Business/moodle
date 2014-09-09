<?php
	
	require_once('../config.php');
	require_once('../_lib/form_lib.php');
	require_once('../_lib/sql_lib.php');
	

?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Moodle Reports</title>
	<?php echo $OUTPUT->standard_head_html() ?>
    <link href="../_css/styles.css" rel="stylesheet" type="text/css" />
	<style type="text/css" title="currentStyle">
			@import "../_datatables/media/css/demo_page.css";
			@import "../_datatables/media/css/demo_table_jui.css";
			@import "../_datatables/themes/smoothness/jquery-ui-1.8.4.custom.css";
	</style>
    <!--[if IE]>
        <link href="../_css/iefixes.css" rel="stylesheet" type="text/css" />
    <![endif]--> 
    <!--[if IE 9]>
        <link href="../_css/ie9fixes.css" rel="stylesheet" type="text/css" />
    <![endif]--> 
    <link type="text/css" href="../_css/ui-lightness/jquery-ui-1.8.4.custom.css" rel="stylesheet" />
    	
	<script type="text/javascript" src="../_js/jquery-1.4.2.min.js"></script>
    <script type="text/javascript" src="../_js/jquery.validate.min.js"></script>
	<script type="text/javascript" src="../_js/jquery-ui-1.8.4.custom.min.js"></script>
    <script type="text/javascript" src="../_js/custom.js"></script>
    <script type="text/javascript" src="../_js/custom.hr.js"></script>
    <script type="text/javascript" src="../_js/custom.scorm.js"></script>
    <script type="text/javascript" src="../_js/custom.coursecat.js"></script>
    

</head>

<body>
<?php include "../_inc/header.php"; ?>


<h2 class="report-header">Reports: HR Profile Data Report (Scorm Courses)</h2>
<div class="links">
    <p><a href="<?php echo $CFG->wwwroot ?>"><?php echo $SITE->fullname; ?></a> &raquo; <a href="<?php echo $linkBackToReports ?>">Reports</a> &raquo; HR Profile Data Report (Scorm Courses) </p>
</div>


<?php

if (isadmin()) { ?>

	<!--<h3>Report by Profile Data</h3>-->
    
     <p class="description">Please note that this only returns users who have been assigned to a group. Remember the hierarchy: Course-Module-Lesson. This report will return all modules and lessons of the selected course. The list of courses is not dependent on the selected group. Reports can take a long time to generate and therefore it is recommended that you use the filtering options where possible. </p>
     
	<form action="<?php echo $CFG->wwwroot . "/admin/dynamic_admin/dynamic_reports/report.php?type=byHR";  ?>" method="POST" id="styled-form">
    <div class="form-section-builder first">
			<label>Profile Field:</label><div class="checkbox-group-outer"><?php build_fields_formlist("byConfig") ;?></div>
    </div>
    <div class="form-section-builder first">
			<label>Profile Data:</label><div class="checkbox-group-outer"><div class='checkbox-group ajax-cbg'><p class='no-data'>-- no data available --</p></div><div class="button-holder"><button type="button" class='check-options'>Select All</button><button type="button" class='uncheck-options'>Clear</button></div></div>
    </div>
    <div class="form-section-builder">
            <label>Category:</label><div class="checkbox-group-outer"><?php build_category_dropdown(true,true);?></div>
    </div>
    <div class="form-section-builder">
            <label>Courses:</label><div class="checkbox-group-outer"><?php build_courses_formlist();?><div class="button-holder"><button type="button" class='check-options'>Select All</button><button type="button" class='uncheck-options'>Clear</button></div></div>
    </div>
    <div class="form-section-builder">      
            <label>Status:</label><div class="checkbox-group-outer"><?php build_status_formlist();?><div class="button-holder"><button type="button" class='check-options-status'>Select All</button><button type="button" class='uncheck-options-status'>Clear</button></div></div>
    </div>
    <div class="form-section-builder">  
            <label>Date from:</label>
            <div class="checkbox-group-outer"><input type="text" id="datepickerfrom" name="datepickerfrom" value="<?php echo $startDateForPicker; ?>" class='datepicker'/></div><br />
            <label>Date to:</label>
            <div class="checkbox-group-outer"><input type="text" id="datepickerto" name="datepickerto" value='<?php echo date("d/m/Y"); ?>' class='datepicker' /></div>
     </div>
     <div class="form-section-builder last"> 
            <input type="submit" value="Submit" />
     </div>
      </form>

     
     <!-- Search by name -->  
    
<?php }else{ ?>
		<p class="no-permission">You do not have the correct permissions to view this page</p>
<?php	} ?> 
<?php
	
	echo $footer;
	
?>

</body>
</html>
