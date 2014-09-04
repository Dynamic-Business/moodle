<?php

/*
To do
-- only show courses in drop down which haven't already been assigned to the group
-- prevent duplicates in database using the index keys (somehow)
-- Javascript confirmation boxes
*/

	require_once('../config.php');
	require_once('../_lib/form_lib.php');
	$function = "add-course";
	$groupId = filter_var( $_GET['id'], FILTER_SANITIZE_NUMBER_INT);
	//$row = $DB->get_record_sql("SELECT name FROM mdl_dynamic_group WHERE id=" . $groupId);
	$row = $DB->get_record_sql('SELECT name FROM {dynamic_group} WHERE id= ? ', array($groupId));
	$groupname = $row->name;
	$action = "";
	$courseIdForm = "";
	if (isset( $_GET['courseid'])){$courseId = filter_var( $_GET['courseid'], FILTER_SANITIZE_STRING); }
	if (isset( $_GET['action'])){$action = filter_var( $_GET['action'], FILTER_SANITIZE_STRING);}
	if (isset( $_POST['courses'])){$courseIdForm = filter_var( $_POST['courses'], FILTER_SANITIZE_STRING);}
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Moodle Enrolments</title>
		<?php echo $OUTPUT->standard_head_html() ;?>
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
<h2 class="enrolments">Group &amp; Enrolment Management: Add Course to Group "<?php echo $groupname; ?>"</h2>
<div class="links">
    <p><a href="<?php echo $CFG->wwwroot ?>"><?php echo $SITE->fullname; ?></a> &raquo; <a href="<?php echo $linkBackToEnrolments ?>">Group &amp; Enrolment Management</a> &raquo; Add Course to Group "<?php echo $groupname; ?>"</p>
</div>

<?php


if(isset($_SESSION['permission:addcourses'])){
	
	//IF DELETE BUTTON WAS PRESSED
	if ($action == "delete"){
		
		$DB->delete_records("dynamic_courses_groups", array('course_id'=>$courseId, 'group_id'=>$groupId));
		//echo "<p class='highlight'>Property id: <b>" . $propertyid . "</b> removed successfully.</p>";
		echo "<p class='highlight'>Property removed successfully.</p>";

	//If Add button was pressed
	}else if($action == "add" ){

		$record = new stdClass();
		$record->group_id	= $groupId ;
		$record->course_id 	= $courseIdForm;
		$DB->insert_record('dynamic_courses_groups', $record, false);
		echo "<p class='highlight'>Course was added successfully.</p>";
			
	}
	//======================
	
		//LIST OF COURSES FOR GROUP
		$sql  = "
			#SELECT * FROM mdl_dynamic_courses_groups dcg
			SELECT dcg.course_id,dcg.group_id,fullname FROM mdl_dynamic_courses_groups dcg
			LEFT JOIN mdl_course ON dcg.course_id=mdl_course.idnumber 
			WHERE dcg.group_id = ? 
			ORDER BY mdl_course.idnumber";
		$result = $DB->get_records_sql($sql,array($groupId));
		
		$sql2 = "SELECT mdl_dynamic_group.name FROM mdl_dynamic_group WHERE mdl_dynamic_group.id =?";
		$result2 = $DB->get_record_sql($sql2,array($groupId));
		
		$sql3 = "SELECT idnumber,fullname,group_id
								FROM mdl_course 
								LEFT JOIN (
									SELECT * FROM
									mdl_dynamic_courses_groups
									WHERE group_id =?
								) AS dcg ON mdl_course.idnumber = dcg.course_id
								WHERE idnumber != ''
								AND group_id IS NULL";
		$result3 = $DB->get_records_sql($sql3,array($groupId));
								
		$groupName = $result2->name;
		
		?>
        
        <?php	include('../_inc/inc.inner-group-admin.php');  ?>
        
        <div class="form-area">
        <h3>Add a Course</h3>
		<form action="<?php echo $_SERVER["PHP_SELF"] . "?action=add&id=" . $groupId . "&courseIdForm=" .  $courseIdForm ?>" method="POST" id="styled-form">
        	<div class="form-section">
        	<label>Course:</label>
        	 <div class="checkbox-group-outer">
        	<select name="courses">
           
        <?php
		

		foreach($result3 as $row3){
				echo "<option value=\"" . $row3->idnumber . "\">". $row3->fullname . "(" . $row3->idnumber . ")" . "</option>"; 
		}
		 
		?>

            </select>
        </div>
            </div>
                <div class="form-section last">
                <input type="submit" value="Add Course" />
            </div>
            <input type="hidden" value="submitted">
		</form>
        
        </div>
        
        <h3>Courses for this Group</h3>
		<?php 
		//echo "<pre>";
		//var_dump($result);die; 
		
		?>
        <table cellpadding="0" cellspacing="0" border="0" class="display" id="styled-table">
        <thead>
            <tr>
                <th width=100> Course Id</th>
                <th>Course Name</th>
                <th>Options</th>
    
            </tr>
        </thead>
        <tbody>
        
        
        <?php
		
		foreach($result as $row){
		 	echo "<tr><td>" . $row->course_id . " </td>";
			echo "<td> ";
			echo  $row->fullname != "" ? $row->fullname : "<span style='color:red'>Course no longer exists or idnumber has been removed.</span>";
			echo "</td>";
			echo "<td class='admin'><a href=\"" . $_SERVER['PHP_SELF'] . "?id=" . $groupId  .  "&courseid=". $row->course_id ."&action=delete\" onclick=\"return confirm('Are you sure?')\"><img src='../_img/delete.png' alt=\"Delete\" title='Delete Property From Group'/></a></td></tr>";
		  
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
