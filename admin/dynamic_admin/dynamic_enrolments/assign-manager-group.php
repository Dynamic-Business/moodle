<?php

/*
To do
-- only show courses in drop down which haven't already been assigned to the group
-- prevent duplicates in database using the index keys (somehow)
-- Javascript confirmation boxes

*/

	require_once('../config.php');
	require_once('../_lib/form_lib.php');
	//
	$function = "assign-manager";

	//$groupId = $_GET['id'];
	$groupId = filter_var( $_GET['id'], FILTER_SANITIZE_NUMBER_INT);

	//$row = $DB->get_record_sql("SELECT name FROM mdl_dynamic_group WHERE id=\"" . $groupId ."\"");
	$row = $DB->get_record_sql('SELECT name FROM {dynamic_group} WHERE id= ? ', array($groupId));

	$groupname = $row->name;
	$action = "";
	if(isset($_GET['action'])){
		//$action = $_GET['action'];
		$action= filter_var( $_GET['action'], FILTER_SANITIZE_STRING);
	}
	if(isset($_POST['userid'])){
		//$userid = $_POST['userid'];
		$userid = filter_var( $_POST['userid'], FILTER_SANITIZE_NUMBER_INT);

	}else if(isset($_GET['userid'])){
		//$userid = $_GET['userid'];
		$userid = filter_var( $_GET['userid'], FILTER_SANITIZE_NUMBER_INT);
	}
	if(isset($_GET['groupid'])){
		//$groupid = $_GET['groupid'];
		$groupid = filter_var( $_GET['groupid'], FILTER_SANITIZE_NUMBER_INT);
	}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Moodle Enrolments</title>
	<?php echo $OUTPUT->standard_head_html() ;?>
		<link href="../_css/styles.css" rel="stylesheet" type="text/css" />
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


<script type="text/javascript">
	function confirmation(id,person) {
	var answer = confirm("Are you sure you want to delete "+person+" (id:"+ id  +")?");
	if (answer){
		window.location = "<?php echo $_SERVER['PHP_SELF']; ?>" + "?id=" + id + "&action=delete";
	}
	else{
	}
}
</script>

</head>

<body>
<?php include "../_inc/header.php"; ?>
<h2 class="enrolments">Group &amp; Enrolment Management: Assign Group Manager "<?php echo $groupname; ?>"</h2>
<div class="links">
    <p><a href="<?php echo $CFG->wwwroot ?>"><?php echo $SITE->fullname; ?></a> &raquo; <a href="<?php echo $linkBackToEnrolments ?>">Group &amp; Enrolment Management</a> &raquo; Assign Group Manager "<?php echo $groupname; ?>"</p>
</div>


<?php


	if(isset($_SESSION['permission:assignmanager'])){
	    	
		if($action == "delete"){
			//$query =  "DELETE FROM dynamic_managers_group WHERE userid=\"" . $userid  .   "\" AND groupid=\"" . $groupId . "\"";
			$DB->delete_records("dynamic_managers_group", array('userid'=>$userid , 'groupid'=>$groupId ));
			echo "<p class='highlight'>Manager: <b>" . $userid . "</b> has been successfully removed</p>";
				
		}
		
		if($action == "add"){
			$record = new stdClass();
			$record->userid		= $userid ;
			$record->groupid 	= $groupId;
			$DB->insert_record('dynamic_managers_group', $record, false);
			echo "<p class='highlight'>Manager: <b>" . $userid . "</b> has been added successfully.</p>";		
		}
		
		?>
        
        	<!-- ASSIGN MANAGER FORM -->
            <!--<p class="description">A user must be assigned the role of 'Group Manager' within Moodle to appear in the list of available managers below. Go to 'Users' - 'Assign System Roles' to assign a user as a Group Manager. 
</p>-->
            
            <?php	include('../_inc/inc.inner-group-admin.php');  ?>
     		
            <div class="form-area">
            
            <h3>Assign a Group Manager</h3>

            <?php
				//Get list of potential managers
				/*$sql = "SELECT DISTINCT(u.id), u.firstname, u.lastname,dmg.groupid FROM mdl_user AS u
							INNER JOIN mdl_role_assignments AS ra ON u.id = ra.userid 
							LEFT JOIN (SELECT * FROM mdl_dynamic_managers_group AS dmg WHERE dmg.groupid = " . $groupId  . ") AS dmg ON u.id = dmg.userid 
							WHERE ra.roleid = " . $GroupManagerRoleID  . "
							AND dmg.groupid IS NULL
							ORDER BY u.firstname";*/
							
				$gids = implode(",", $GroupManagerRoleID);
				$sql = "SELECT DISTINCT(u.id), u.firstname, u.lastname,dmg.groupid FROM mdl_user AS u
							INNER JOIN mdl_role_assignments AS ra ON u.id = ra.userid 
							LEFT JOIN (SELECT * FROM mdl_dynamic_managers_group AS dmg WHERE dmg.groupid = ?) AS dmg ON u.id = dmg.userid " . 
							//WHERE ra.roleid = " . $GroupManagerRoleID  . "
							" WHERE ra.roleid IN ( " . $gids . ") 
							 AND dmg.groupid IS NULL
							ORDER BY u.firstname";
				
				$resultManagers = $DB->get_records_sql($sql,array($groupId));
				
				if(!$resultManagers){ 
					echo "<p class='info'>All available managers have been assigned to this group <i>or</i> there are no available managers remaining</p>";  
				}else{ ?>
                
				<form action="<?php echo $_SERVER['PHP_SELF'] . "?action=add&id=" . $groupId ?>" method="POST" id="styled-form" >	
                    <div class="form-section">
                        <label for="name">Group Manager:</label>
                        <SELECT name="userid">
                        
                    	<?php
						foreach($resultManagers as $row){
                            echo "<option value='" . $row->id   . "'>". $row->firstname . " " . $row->lastname . "</option>";
                        }
                     	?>
                        </SELECT>
                    </div>
                    
                    <div class="form-section last">
                        <input name="submit" type="submit" value="Assign"/>
                        <input type="hidden" value="submitted">
                    </div>
                    
                    <?php
				}	
				?>

   				</form>
                
                </div>
    
    			<!-- END OF ASSIGN MANAGER FORM -->
        
        <h3>Assigned Managers</h3>
        <table cellpadding="0" cellspacing="0" border="0" class="display" id="styled-table">
        <thead>
            <tr>
                <th>First Name</th>
                <th>Surname</th>
                <th>Options</th>
    
            </tr>
        </thead>
        <tbody>
        <?php
		
		$sql = "SELECT u.id, u.firstname,u.lastname FROM mdl_user AS u, mdl_dynamic_managers_group AS dmg WHERE u.id = dmg.userid AND groupid=?";
		$resultUsers = $DB->get_records_sql($sql,array($groupId ));	
		
		foreach($resultUsers as $row){
				echo "<tr><td> " . $row->firstname . " </td><td> " . $row->lastname . " </td>
				<td class='admin'><a href=\"" . $_SERVER['PHP_SELF'] . "?id=" . $groupId  .  "&userid=".$row->id."&action=delete\" onclick=\"return confirm('Are you sure?')\"><img src='../_img/delete.png' alt=\"Delete\" title='Delete Property From Group'/></a></td></tr>";
				
			}

		?>
     
     	</tbody>
		</table>
		
		<?php 

	}else{
		echo "<p class='no-access'>You do not have permission to view this page</p>";
	}
?> 
<?php
	
	echo $footer;
	
?>

</body>
</html>
