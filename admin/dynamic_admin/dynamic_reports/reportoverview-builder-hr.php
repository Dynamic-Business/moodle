<?php
	
	require_once('../config.php');
	require_once('../_lib/form_lib.php');
	require_once('../_lib/sql_lib.php');
	

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
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
    <script type="text/javascript" src="../_js/custom.overview.js"></script>
    

</head>

<body>
<?php include "../_inc/header.php"; ?>


<h2 class="report-header">Reports: Overview Report</h2>
<div class="links">
    <p><a href="<?php echo $CFG->wwwroot ?>"><?php echo $SITE->fullname; ?></a> &raquo; <a href="<?php echo $linkBackToReports ?>">Reports</a> &raquo; Overview Report </p>
</div>


<?php

if (isadmin()) { ?>

	<!--<h3>Report by Profile Data</h3>-->
    
     <p class="description">This report gives you an overview of all users who have been enrolled on a course against the number of completions (as a percentage). This report may take a long time to load so try to select the minimum number of fields from 'Profile Data' and 'Courses' where possible.</p>
     
	<form action="<?php echo $CFG->wwwroot . "/admin/dynamic_admin/dynamic_reports/report.php?type=byOverview";  ?>" method="POST" id="styled-form">
    <div class="form-section-builder first">
			<label>Profile Field:</label><div class="checkbox-group-outer"><?php build_fields_formlist("byConfig") ;?></div>
    </div>
    <div class="form-section-builder first">
			<label>Profile Data:</label><div class="checkbox-group-outer"><div class='checkbox-group ajax-cbg'><p class='no-data'>-- no data available --</p></div><div class="button-holder"><button type="button" class='check-options'>Select All</button><button type="button" class='uncheck-options'>Clear</button></div></div>
    </div>
<!--     <div class="form-section-builder">
            <label>Category:</label><?php build_category_dropdown();?>
    </div> -->
    <div class="form-section-builder">
            <label>Courses:</label><div class="checkbox-group-outer"><?php build_courses_formlist();?><div class="button-holder"><button type="button" class='check-options'>Select All</button><button type="button" class='uncheck-options'>Clear</button></div></div>
    </div>
    <div class="form-section-builder">      
            <label>Report Type:</label><div class="checkbox-group-outer"><div class="radio-holder"><input type="radio" name="overviewtype" value="accumulative" checked=true />Accumulative Courses <input type="radio" name="overviewtype" value="separate" />Separate Courses</div></div>
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
