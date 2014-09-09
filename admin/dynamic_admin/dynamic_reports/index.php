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

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" >
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
    	
        <?php if(isadmin() ){ ?>
        	<li><a href="reportoverview-builder-hr.php" >Overview Report</a></li>
        <?php } ?> 
    	<?php if(isadmin() ){ ?>
			<li><a href="reportcourse-builder-hr.php" >HR Profile Data Report (Course Completions)</a></li>
            <li><a href="reportscorm-builder-hr.php" >HR Profile Data Report (Scorm Activities)</a></li>
            <li class="separator"><a href="reportquiz-builder-hr.php" >HR Profile Data Report (Quiz Activities)</a></li>
            <!--<li class="separator"><a href="reportclassroom-builder-hr.php" >HR Profile Data Report (Classroom Sessions)</a></li>-->
        <?php } ?>
		<?php 
		if(isadmin() ){ ?>
        	<li class="separator"><a href="report.php?type=viewManagers">View all Group Managers</a> </li> 
        <?php } ?>
		<h3 class='red'>Main Report</h3>
		<p>The Learning Tracker report shows completed topics of a training programme, e.g. Retail Academy.</p>
		<li><a href="reportactivity-builder.php">Learning Tracker</a></li><!-- (Manual Group Report (Activity Completions))-->

		<p>The Individual Learning report shows what training has been completed, or is outstanding, for a specific member of staff.</p>
		<li class='exspace'><a href="reportuser-builder.php">Individual Learning Report</a></li><!--(Individual User Report (Course Completions))-->
		
		<h3>Other Reports</h3>
		<p>The Exception report shows people that are behind with their training (Retail Academy and Apprentice Programmes only).</p>
		<li class="exspace"><a href="reportstoreprogress-builder.php" >Exception Report</a></li>

		<p>The Progress report shows training that's not been started, or has been completed.</p>
		<li class='exspace'><a href="reportcourse-builder.php">Progress Report</a></li><!--(Manual Group Report (Course Completions))-->

		<!--<p>The Quiz Results report shows quiz scores and the number of attempts.</p>
		<li class='exspace'><a href="reportquiz-builder.php">Quiz Results</a></li><!-- (Manual Group Report (Quiz Activities))-->
		<!--<p>The E-learning Trackers report shows e-learning within training sessions that needs to be, or has been completed.</p>
    	<li class='exspace'><a href="reportscorm-builder.php">E-learning Trackers</a></li><!-- (Manual Group Report (Scorm Activities))-->

        
       <!-- <li class="separator"><a href="reportclassroom-builder.php" >Manual Group Report (Classroom Sessions)</a></li>-->
        <!-- skips the builder screen and goes directly to report -->
       
        
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
