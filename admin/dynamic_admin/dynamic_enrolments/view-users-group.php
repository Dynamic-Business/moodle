<?php

/*
To do
-- only show courses in drop down which haven't already been assigned to the group
-- prevent duplicates in database using the index keys (somehow)
-- Javascript confirmation boxes


*/

	require_once('../config.php');
	//$groupId = $_GET['id'];
 	$groupId = filter_var( $_GET['id'], FILTER_SANITIZE_NUMBER_INT);

	$sql = ("SELECT name FROM mdl_dynamic_group WHERE id=\"" . $groupId ."\"");
	$row = $DB->get_record_sql($sql);

	$groupname = $row->name;
	$function = "view-users";
	$showWarningMessage = FALSE;
	//if (isset( $_GET['orderby'])){ $orderBy = $_GET['orderby'] ; } else{ $orderBy = "lastname";   }
	if (isset($_GET['orderby'])){$orderBy =filter_var($_GET['orderby'], FILTER_SANITIZE_STRING);} else{ $orderBy = "lastname";}

	$selfPageRef = $_SERVER['PHP_SELF'] . "?id=" . $groupId ;

	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Moodle Enrolments </title>
	<?php echo $OUTPUT->standard_head_html() ?>
	<link href="../_css/styles.css" rel="stylesheet" type="text/css" />
	<?php require_once('../_inc/inc.iefixes.php'); ?>
  	<style type="text/css" title="currentStyle">
			@import "../_datatables/media/css/demo_page.css";
			@import "../_datatables/media/css/demo_table_jui.css";
			@import "../_datatables/themes/smoothness/jquery-ui-1.8.4.custom.css";
	</style>
		<script type="text/javascript" language="javascript" src="../_datatables/media/js/jquery.js"></script>
		<script type="text/javascript" language="javascript" src="../_datatables/media/js/jquery.dataTables.js"></script>
		<script type="text/javascript" charset="utf-8">
			$(document).ready(function() {
				oTable = $('#styled-table').dataTable({
					"bJQueryUI": true,
					"sPaginationType": "full_numbers",
					"bRetrieve": true
				});
			} );
		</script>


        
        
</head>

<body class='enrolments'>
	<?php include "../_inc/header.php"; ?>
	<h2 class="enrolments">Group &amp; Enrolment Management: View Users In Group "<?php echo $groupname; ?>"</h2>

	<div class="links">
		<?php //echo $linkBackToEnrolments ?>
		<p><a href="<?php echo $CFG->wwwroot ?>"><?php echo $SITE->fullname; ?></a> &raquo; <a href="<?php echo $linkBackToEnrolments ?>">Group &amp; Enrolment Management</a> &raquo; View Users In Group "<?php echo $groupname; ?>"</p>
	</div>


<?php


	if(isset($_SESSION['permission:viewusers'])){
    

		//1. First display the Group Name in the table header	
		//$queryGroupName = "SELECT dynamic_group.name FROM dynamic_group WHERE dynamic_group.id =\"" . $groupId . "\"";
		//$resultGroupName = mysql_query($queryGroupName);
		//$groupName = mysql_fetch_row($resultGroupName);
		
		//2. Query to show all users in group
		$queryUsers = "
		SELECT u.id,idnumber,firstname,lastname,ud.datestarted AS 'datestarted' 
		FROM mdl_dynamic_usersgroups dug
		INNER JOIN mdl_user u ON u.id = dug.userid
		INNER JOIN mdl_dynamic_userdata ud On u.id = ud.userid
		 WHERE dug.groupid = " .$groupId. " AND u.deleted != 1";

		$_SESSION['query'] = $queryUsers; 
		$resultUsers  = $DB->get_records_sql($queryUsers);

		
		
?>
      <?php	include('../_inc/inc.inner-group-admin.php');  ?>  

 <div id="admin-buttons" >
     <button class='but' onClick="window.location='../dynamic_reports/download.php?type=usersingroup' ">Download to Excel</button>
 </div>

    <div class="demo_jui view-managers">
        <table cellpadding="0" cellspacing="0" border="0" class="display" id="styled-table">
            <thead>
                <tr>
                    <th width=100>Id Number</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Start Date</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            //while($row = mysql_fetch_array($resultUsers)){
			foreach($resultUsers as $row) {
				
				if ($row->idnumber == NULL){echo "<tr class='red'>";$showWarningMessage = TRUE;}else{ echo "<tr>";}
				echo "<td>" . $row->idnumber . " </td><td> " . $row->firstname . " </td><td> " . $row->lastname;
				echo "</td><td>" . date('d/m/Y',$row->datestarted) . " </td></tr>";
             }
                    
            ?>
            </tbody>
        </table>
	</div>
    
     
            
 <?php

	}else{
		echo "<p class='no-access'>You do not have permission to view this page</p>";
	}
?> 

<?php 

echo $footer;
//mysql_close($con);

 ?>            
            
    
</body>

</html>
