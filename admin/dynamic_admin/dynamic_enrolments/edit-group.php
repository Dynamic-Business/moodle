<?php

	require_once('../config.php');
	$function = "edit-group";
	//$groupId = $_GET['id'];
  $groupId = filter_var( $_GET['id'], FILTER_SANITIZE_NUMBER_INT);
	$row = $DB->get_record_sql("SELECT name FROM mdl_dynamic_group WHERE id=\"" . $groupId ."\"");
	$groupname = $row->name;
	$action = "";

  /*if (isset($_GET['action'])){$action = $_GET['action'];}
  if (isset($_POST['name'])){$groupname = $_POST['name'];}
  if (isset($_POST['description'])){$groupdesc = $_POST['description'];}
  if (isset($_POST['grouptype'])){$grouptype = $_POST['grouptype'];} else{ $grouptype = "";}
  if (isset($_POST['academy'])){$academy = $_POST['academy'];} else{ $academy = 0;}*/

  if (isset($_GET['action'])){$action = filter_var( $_GET['action'], FILTER_SANITIZE_STRING);} else{$action = "";}
  if (isset($_POST['name'])){$groupname = filter_var( $_POST['name'], FILTER_SANITIZE_STRING);} else { $groupname = "";}
  if (isset($_POST['description'])){$groupdesc = filter_var( $_POST['description'], FILTER_SANITIZE_STRING);} else{ $groupdesc = "";}
  if (isset($_POST['grouptype'])){$grouptype = filter_var( $_POST['grouptype'], FILTER_SANITIZE_STRING);} else{ $grouptype = "";}
  if (isset($_POST['academy'])){$academy = filter_var( $_POST['academy'], FILTER_SANITIZE_STRING);} else{ $academy = 0;}
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Moodle Enrolments</title>
		<?php echo $OUTPUT->standard_head_html() ;?>

		<link href="../_css/styles.css" rel="stylesheet" type="text/css" />
        <!--[if IE 7]>
       	 		<link href="../_css/iefixes.css" rel="stylesheet" type="text/css" />
       	 		<link href="../_css/ie7fixes.css" rel="stylesheet" type="text/css" />
        <![endif]-->
     
</head>

<body>
<?php include "../_inc/header.php"; ?>
<h2 class="enrolments">Group &amp; Enrolment Management: Edit Group "<?php echo $groupname; ?>"</h2>
<div class="links">
    <p><a href="<?php echo $CFG->wwwroot ?>">Moodle</a> &raquo; <a href="<?php echo $linkBackToEnrolments ?>">Group &amp; Enrolment Management</a> &raquo; Edit Group "<?php echo $groupname; ?>"</p>
</div>

   

<?php

	if(isset($_SESSION['permission:editgroup'])){
    	
		
		if($action == "edit"){
		
			$gprecord = new stdClass();
			$gprecord->id = $groupId;
            $gprecord->name = $groupname;
            $gprecord->description = $groupdesc;
			$gprecord->grouptype = $grouptype;
			$gprecord->academy = $academy;
			
			$DB->update_record("dynamic_group", $gprecord);
			echo "<p class='highlight'>Group: <b>" . $groupname . "</b> has been edited successfully.</p>";
				
		}
		
		$row = $DB->get_record_sql("SELECT name,description,grouptype,academy FROM mdl_dynamic_group WHERE id=?",array($groupId));

		?>
    	<?php	include('../_inc/inc.inner-group-admin.php');  ?>
        
        
    	 <form action="<?php echo $_SERVER['PHP_SELF'] . "?action=edit&id=" . $groupId ?>" method="POST" id="styled-form" class="small-width">
         	<div class="form-section">
                <label for="name">Group Name:</label>
                <input name="name" type="text" id="name" value="<?php echo $row->name;?>" />
            </div>
            <div class="form-section">
                <label for="name">Group Description:</label>
                <textarea name="description"  ><?php echo $row->description;?></textarea>
            </div>

              <div class="form-section">   
                  <label for="name">Academy Group:</label>
                  <div class="checkbox-group-outer">
                      <div class="radio-holder">
                        <input type="radio" name="academy" value="1" <?php if ($row->academy == '1'){ echo "checked='true'";} ?> />Yes<input type="radio" name="academy" value="0" <?php if ($row->academy == '0'){ echo "checked='true'";} ?> />No<br />
                      </div>
                  </div>
              </div>


            <div class="form-section last">
                <input name="submit" type="submit" value="Update" />
            </div>
            <input type="hidden" value="submitted" />
   		 </form>
         
         
         
    
		<?php //IF FORM IS SUBMITTED

	}else{
		echo "<p class='no-access'>You do not have permission to view this page</p>";
	}
?> 
<?php
	
	echo $footer;
	
?>

</body>
</html>
