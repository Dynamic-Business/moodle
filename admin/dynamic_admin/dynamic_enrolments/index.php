<?php
	
	require_once('../config.php');
	require_once($CFG->libdir . '/adminlib.php');
	require_once('../_lib/sql_lib.php');
	//
	$groupId = "";
	if(isset($_GET['id'])){
		//$groupId = $_GET['id'];
 		 $groupId = filter_var( $_GET['id'], FILTER_SANITIZE_NUMBER_INT);
	}
	$action = "";
	if(isset($_GET['action'])){
		//$action = $_GET['action'];
		$action = filter_var( $_GET['action'], FILTER_SANITIZE_STRING);
	}
	if(isset($_GET['groupname'])){
		//$groupname = $_GET['groupname'];
		$groupname = filter_var( $_GET['groupname'], FILTER_SANITIZE_STRING);
	}
	//For returning permissions from DB
	if (isadmin() || ismanager()){
		if (isadmin()){
			$role = "admin";
		}else if (ismanager()){
			$role = "manager";
		}
		storePermissions($role);
	}
	

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Group Enrolments</title>

		<?php echo $OUTPUT->standard_head_html() ?>
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
	function confirmation(groupId,groupName) {
	var answer = confirm("Are you sure you want to delete "+groupName+" (id:"+ groupId  +")?");
	if (answer){
		//alert("Entry Deleted")
		//window.location = "links.php?act=trackdelete&id="+ID;
		window.location = "<?php echo $SERVER['php_self']; ?>" + "?id=" + groupId + "&action=delete";
	}
	else{
		//alert("No action taken")
	}
}
</script>



</head>

<body>

	<?php include "../_inc/header.php"; ?>
    

<h2 class="enrolments">Group &amp; Enrolment Management</h2>
<div class="links">
	<?php //echo $linkBackToEnrolments ?>
    <p><a href="<?php echo $CFG->wwwroot ?>">Moodle</a> &raquo; Group &amp; Enrolment Management</p>
</div>

<?php

if (isadmin() || ismanager() ) {
	
	// LIST OF USERS

	
	//If delete group was clicked
	if ($action == "delete"){
	
		$DB->delete_records("dynamic_group", array('id'=>$groupId));
		$DB->delete_records("dynamic_courses_groups", array('group_id'=>$groupId));
		$DB->delete_records("dynamic_propertiesforgroup", array('groupid'=>$groupId));
		$DB->delete_records("dynamic_managers_group", array('groupid'=>$groupId));
		//new
		$DB->delete_records("dynamic_usersgroups", array('groupid'=>$groupId)); //overnight script
		//$DB->delete_records("dynamic_groupdata", array('groupid'=>$groupId)); //replaces propertiesforgroup
		
		echo "<p class='highlight'>Group removed successfully.</p>";
		
	}
	
	if (isadmin()){ //show all groups
		$result2 = $DB->get_records_sql("SELECT * FROM mdl_dynamic_group");
		
	}else{ //show only the groups assigned to this manager
	
		$result2 = $DB->get_records_sql("SELECT * FROM mdl_dynamic_managers_group AS dmg
										INNER JOIN mdl_dynamic_group AS dg ON dmg.groupid = dg.id
										WHERE userid = ?", array($USER->id));
				
	}


	//$result2 = mysql_query($sql);
	
	
	?>
    <div id="admin-buttons">
    	
		<?php 
            if(isset($_SESSION['permission:creategroup'])){
				?>
                    
                    <button class='but' onClick="window.location='create-group.php'">New Group</button>
                    
                    
                <?php
            }
        ?>
    </div>
    
    <table cellpadding="0" cellspacing="0" border="0" class="display" id="styled-table">
	<thead>
		<tr>
        	<th width=20>Id</th>
			<th>Name</th>
			<th>Description</th>
            <th>Academy Group</th>
            <th>Definitions Set</th>
            <th>Courses Enrolled</th>
			<th>Options</th>

		</tr>
	</thead>
    <tbody>
    
    <?php 
   // while($row = mysql_fetch_array($result2))
	foreach ($result2 as $row) 
	  {
	  
	  echo "<tr><td>" . $row->id . " </td><td><a class='table-link' href='edit-group.php?id=" . $row->id . "'> " . $row->name . " </a></td><td> " . $row->description. " </td><td> " . convertGroupTypeDisplay($row->academy). " </td><td>" . getDefinitions($row->id) . "</td><td>" . getCourseIds($row->id)  . "</td>";
	  echo "<td class='admin'>";
	  if(isset($_SESSION['permission:editgroup'])){
	  	echo "<a href=\"edit-group.php?id=" . $row->id . "\"><img src='../_img/users_edit.png' alt='Edit Group' title='Edit Group'/></a>";
	  }
	  if(isset($_SESSION['permission:definegroup'])){
	  	echo "<a href=\"define-group.php?id=" . $row->id . "&pfield=\"><img src='../_img/users_process.png' alt='Define Group' title='Define Group'/></a>";
	  }
	  if(isset($_SESSION['permission:addcourses'])){
	  	echo "<a href=\"add-course.php?id=" . $row->id . "\"><img src='../_img/application_add.png' alt='Add Courses' title='Add Courses'/></a>";
	  }
	  if(isset($_SESSION['permission:viewusers'])){
	  	echo "<a href=\"view-users-group.php?id=" . $row->id . "\"><img src='../_img/users_search.png' alt='View Users' title='View Users' /></a>";
	  }
	  if(isset($_SESSION['permission:assignmanager'])){
	  	echo "<a href=\"assign-manager-group.php?id=" . $row->id . "\"><img src='../_img/business_user_add.png' alt=\"Assign Manager\" title='Assign Manager'/></a>";
	  }
	  if(isset($_SESSION['permission:deletegroup'])){
	  	echo "<a href=\"" . $_SERVER['PHP_SELF'] . "?id=" . $row->id  .  "&action=delete\" onclick=\"return confirm('Are you sure?')\"><img src='../_img/delete.png' alt=\"Delete\" title='Delete Group'/></a></td>";
	  }
	  echo "</td></tr>";
	  
	  }
	?>
    </tbody>
    </table>
    
    
    <?php
	

}else{
	echo "<p class='no-permission'>You do not have the correct permissions to view this page</p>";
	
}
?> 
<?php
	
	echo $footer;
	
	
?>

</body>
</html>
