<?php
	//session_start();
	require_once('../config.php');
	require_once('../_lib/form_lib.php');
	require_once('../_lib/sql_lib.php');
	
	$_SESSION["userids"] = "";
	
?>

<!DOCTYPE html>
<html>
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
    <script type="text/javascript" src="../_js/custom.user.js"></script>
    

</head>

<body>
<?php include "../_inc/header.php"; ?>


<h2 class="report-header">Reports: Individual User Report</h2>
<div class="links">
    <p><a href="<?php echo $CFG->wwwroot ?>"><?php echo $SITE->fullname; ?></a> &raquo; <a href="<?php echo $linkBackToReports ?>">Reports</a> &raquo; Indiviual User Report</p>
</div>


<?php

if (isadmin() || ismanager()) { ?>

	<!--<h3>Report by Profile Data</h3>-->
    
    <p class="description">Report shows all status for all courses the selected user is currently or has previously been enrolled on. You can select up to a maximum of 5 users per report.</p>
     
	<form action="<?php echo $CFG->wwwroot . "/admin/dynamic_admin/dynamic_reports/report.php?type=byUser";  ?>" method="POST" id="styled-form">
    <div class="form-section-builder first">
			<label>Search User:</label><input type="text" name="search" class="search" /><button type="button" class='but-search'>Search</button></div>
    </div>
    <div class="form-section-builder">
			<label>Search Results:</label><div class="checkbox-group-outer"><div class='checkbox-group ajax-cbg'><p class='no-data'>-- no data available --</p></div><div class="button-holder"><button type="button" class='but-add'>Add User(s)</button></div></div>
    </div>
    <div class="form-section-builder">
            <label>Added Users:<br /><span class="normal">(All users in this box will appear in the report - only use the checkbox to remove a user)</span></label><div class="checkbox-group-outer"><div class='checkbox-group ajax-cbg2'><p class='no-data'>-- no users added --</p></div><div class="button-holder"><button type="button" class='but-remove'>Remove User(s)</button></div></div>
    </div>
    
    
    
    <!--<div class="form-section-builder">      
            <label>Status:</label><div class="checkbox-group-outer"><?php build_status_formlist();?><div class="button-holder"><button type="button" class='check-options-status'>Select All</button><button type="button" class='uncheck-options-status'>Clear</button></div></div>
    </div>-->
    <!--<div class="form-section-builder">  
            <label>Date from:</label>
            <input type="text" id="datepickerfrom" name="datepickerfrom" value="<?php echo $startDateForPicker; ?>" /><br />
            <label>Date to:</label>
            <input type="text" id="datepickerto" name="datepickerto" value='<?php echo date("d/m/Y"); ?>' />
     </div>-->
     
     
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
