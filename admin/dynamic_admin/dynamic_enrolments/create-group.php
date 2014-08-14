<?php

/*
To do
-- only show courses in drop down which haven't already been assigned to the group
-- prevent duplicates in database using the index keys (somehow)
-- Javascript confirmation boxes


*/

	require_once('../config.php');
	//require_once($CFG->libdir.'/adminlib.php');
	//require_once($CFG->libdir.'/tablelib.php');
	$function = "create-group";

	/*if (isset($_GET['action'])){$action = $_GET['action'];} else{$action = "";}
	if (isset($_POST['name'])){$groupname = $_POST['name'];} else { $groupname = "";}
	if (isset($_POST['description'])){$groupdesc = $_POST['description'];} else{ $groupdesc = "";}
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
<title>Moodle Enrolments <?php echo $groupname;?></title>

<?php echo $OUTPUT->standard_head_html() ;?>
<link href="../_css/styles.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php include "../_inc/header.php"; ?>
<h2 class="enrolments">Group &amp; Enrolment Management: Create New Group</h2>
<div class="links">
    <p><a href="<?php echo $CFG->wwwroot ?>">Moodle</a> &raquo; <a href="<?php echo $linkBackToEnrolments ?>">Group &amp; Enrolment Management</a> &raquo; Create New Group</p>
</div>

   

<?php

//$con = mysql_connect("localhost","root","");

	if(isset($_SESSION['permission:creategroup'])){
	
		if($action == "add"){
		
			//connection code went here (deleted)-------------------------------------
			
			//$query =  "INSERT INTO dynamic_group (name, description) VALUES (\"" . $groupname . "\",\"" . $groupdesc . "\")";
			
			$gprecord = new stdClass();
            $gprecord->name = $groupname;
            $gprecord->description = $groupdesc;
			$gprecord->grouptype = $grouptype;
			$gprecord->academy = $academy;
			
			//print_r($gprecord);
			$groupId = $DB->insert_record("dynamic_group", $gprecord , true);
			
			
			if($groupId > 0 ){
				echo "<p class='highlight'>Group: <b>" . $groupname . "</b> has been created successfully. Use the links below to setup your group.</p>";
				$gprecord2 = new stdClass();
				$gprecord2->groupid = $groupId;
				$DB->insert_record("dynamic_groupdata", $gprecord2,false);
			}else{
				echo "<p class='warning-msg'>An error has occurred!</p><br>";
			}
			
	
		}
	
	
	?>
    
    <?php if ($action == 'add'){ include('../_inc/inc.inner-group-admin.php'); } ?>
    
    
    	 <form action="<?php echo $_SERVER['PHP_SELF'] . "?action=add" ?>" method="POST" id="styled-form" class="small-width">
             <div class="form-section">
             <label for="name">Group Name:</label>
             <input name="name" type="text" id="name" />
             </div>
             <div class="form-section">   
              <label for="name">Group Description:</label>
              <textarea name="description" cols="20" rows="4"></textarea><br />
             </div>
             

              <div class="form-section">   
                  <label for="name">Academy Group:</label>
                  <div class="checkbox-group-outer">
                      <div class="radio-holder">
                        <input type="radio" name="academy" value="1"  />Yes<input type="radio" name="academy" value="0" checked="true" />No<br />
                      </div>
                  </div>
              </div>


              <div class="form-section last">  
              <input name="submit" type="submit" value="Submit"/>
              </div>
              <input type="hidden" value="submitted">
   		 </form>
    
    	

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
