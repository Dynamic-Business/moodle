<?php
	require_once('../config.php');
	require_once('../_lib/sql_lib.php'); //needed to import new isadmin/ismanager functions
	
	//For returning permissions from DB
	if (isadmin() || ismanager()){
		if (isadmin()){
			$role = "admin";
		}else if (ismanager()){
			$role = "manager";
		}
		//echo $role;
		storePermissions($role);
		
	}
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Moodle Reports</title>
	<?php echo $OUTPUT->standard_head_html(); ?>

<link href="../_css/styles.css" rel="stylesheet" type="text/css" />
<!--[if IE]>
	<link href="css/iefixes.css" rel="stylesheet" type="text/css" />
<![endif]--> 

</head>

<body>

	<?php include "../_inc/header.php"; ?>
<h2 class="report-header">Reports</h2>
<div class="links">
    <p><a href="<?php echo $CFG->wwwroot ?>"><?php echo $SITE->fullname; ?></a> &raquo; Reports</p>
</div>


<?php

if (isadmin() || ismanager()) { ?>
	<p class="description">Please select from the options below:</p>
	<ul class="report-list">
    	<li class="separator"><a href="reportuser-builder.php">Individual Training Records</a></li><!--(Individual User Report (Course Completions))-->
        <?php if(isadmin() ){ ?>
        	<li class="separator"><a href="reportoverview-builder-hr.php" >Overview Report</a></li>
        <?php } ?> 
    	<?php if(isadmin() ){ ?>
			<li><a href="reportcourse-builder-hr.php" >HR Profile Data Report (Course Completions)</a></li>
            <li><a href="reportscorm-builder-hr.php" >HR Profile Data Report (Scorm Activities)</a></li>
            <li class="separator"><a href="reportquiz-builder-hr.php" >HR Profile Data Report (Quiz Activities)</a></li>
            <!--<li class="separator"><a href="reportclassroom-builder-hr.php" >HR Profile Data Report (Classroom Sessions)</a></li>-->
        <?php } ?>
		<li><a href="reportcourse-builder.php">Progress Report</a></li><!--(Manual Group Report (Course Completions))-->
		<!--<li><a href="#" style='color:#ccc'>Progress Report [UNDER MAINTENANCE]</a></li>-->
		<li><a href="reportactivity-builder.php">Learning Trackers</a></li><!-- (Manual Group Report (Activity Completions))-->
    	<li><a href="reportscorm-builder.php">E-learning Trackers</a></li><!-- (Manual Group Report (Scorm Activities))-->
        <li class="separator"><a href="reportquiz-builder.php">Quiz Results</a></li><!-- (Manual Group Report (Quiz Activities))-->
       <!-- <li class="separator"><a href="reportclassroom-builder.php" >Manual Group Report (Classroom Sessions)</a></li>-->
        <!-- skips the builder screen and goes directly to report -->
       	<?php 
		if(isadmin() ){ ?>
        	<li class="separator"><a href="report.php?type=viewManagers">View all Group Managers</a> </li> 
        <?php } ?>
        
        <?php if(isset($_SESSION['permission:rebuildtables'])){ ?>
        	<li><a href="../_create/createUserDataTb.php">Rebuild UserData Table*</a> </li>
            <li><a href="../_create/createGroupsDataTb.php">Rebuild GroupData Table*</a></li>
            <li><a href="../_create/createUsersGroupsTb.php">Rebuild UserGroups Table*</a></li>
			<li><a href="<?php echo $CFG->wwwroot ?>/admin/cron.php">Run Cron**</a></li>
        <?php } ?> 

    </ul>
    
    <?php if(isset($_SESSION['permission:rebuildtables'])){ ?>
        <p class="description2">*You may need to rebuild the tables if you have created new groups or you have recently updated user data and want to report on them immediately.<br />Use with caution - may take a long time and your website will run slowly during this process. These tables are usually scheduled to be rebuilt every night (depending on your installation).<br /><br />**The Course Status reports rely on Moodle's cron script being run. Please click the 'Run Cron' link to update Course Status manually.</p>
          
    <?php } ?>  
    
    
    
     
     <!-- Search by name -->  
    
<?php  }else{ ?>
		<p class="no-permission">You do not have the correct permissions to view this page</p>
<?php	} ?> 

<?php
	
	echo $footer;
	
?>


</body>
</html>
