<?php

	require_once('../config.php');
	$function = "edit-group";
	//$groupId = $_GET['id'];
    $groupId = filter_var( $_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $row = $DB->get_record_sql("SELECT name FROM mdl_dynamic_group WHERE id={$groupId}");

    $disp_name = $row->name;
    $action = "";

  /*if (isset($_GET['action'])){$action = $_GET['action'];}
  if (isset($_POST['name'])){$groupname = $_POST['name'];}
  if (isset($_POST['description'])){$groupdesc = $_POST['description'];}
  if (isset($_POST['grouptype'])){$grouptype = $_POST['grouptype'];} else{ $grouptype = "";}
  if (isset($_POST['academy'])){$academy = $_POST['academy'];} else{ $academy = 0;}*/

  if (isset($_GET['action'])){$action = filter_var( $_GET['action'], FILTER_SANITIZE_STRING);} else{$action = "";}
  if (isset($_POST['name'])){$groupname = filter_var( $_POST['name'], FILTER_SANITIZE_STRING);} else { $groupname = "";}
  if (isset($_POST['description'])){$groupdesc = filter_var( $_POST['description'], FILTER_SANITIZE_STRING);} else{ $groupdesc = "";}
  if (isset($_POST['dateafter'])){$dateafter = filter_var( $_POST['dateafter'], FILTER_SANITIZE_STRING);} else{ $dateafter = "";}
  if (isset($_POST['datebefore'])){$datebefore = filter_var( $_POST['datebefore'], FILTER_SANITIZE_STRING);} else{ $datebefore = "";}

    if($action == "edit"){

        if($dateafter != "" && $dateafter != "N/A"){
            $da = DateTime::createFromFormat('d/m/Y', $dateafter);
            $dateafter =  $da->getTimestamp();
        }else{
            $dateafter = "";
        }
        if($datebefore != "" && $datebefore != "N/A"){
            $db = DateTime::createFromFormat('d/m/Y', $datebefore);
            $datebefore =  $db->getTimestamp();
        }else{
            $datebefore = "";
        }

        $gprecord = new stdClass();
        $gprecord->id = $groupId;
        $gprecord->name = $groupname;
        $gprecord->description = $groupdesc;
        $gprecord->dateafter = $dateafter;
        $gprecord->datebefore = $datebefore;

        $DB->update_record("dynamic_group", $gprecord);
        $update_message =  "<p class='highlight'>Group: <b>" . $groupname . "</b> has been edited successfully.</p>";

    }



$row = $DB->get_record_sql("SELECT name,description,grouptype,dateafter,datebefore FROM mdl_dynamic_group WHERE id=?",array($groupId));
if ($row->dateafter == 0){
    $dateafter_chkd = " ";
    $dateafter_text = " disabled value='N/A' "; 
}else{
    $disp_after = date('d/m/Y', $row->dateafter);
    $dateafter_chkd = " checked='checked' ";
    $dateafter_text = " value='{$disp_after}' "; 
}
if ($row->datebefore == 0){
    $datebefore_chkd = " ";
    $datebefore_text = " disabled value='N/A' "; 
}else{
    $disp_before = date('d/m/Y', $row->datebefore);
    $datebefore_chkd = " checked='checked' ";
    $datebefore_text = " value='{$disp_before}' "; 
}  


	
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

        <link type="text/css" href="../_css/ui-lightness/jquery-ui-1.8.4.custom.css" rel="stylesheet" />  
        <script type="text/javascript" src="../_js/jquery-1.4.2.min.js"></script>
        <script type="text/javascript" src="../_js/jquery.validate.min.js"></script>
        <script type="text/javascript" src="../_js/jquery-ui-1.8.4.custom.min.js"></script>

        <script type="text/javascript">
        // Form Validation 

        // Date / Checkbox enable
        $(document).ready(function(){

            $("#dateafter,#datebefore").datepicker({maxDate: 'Now',minDate: null});
            /*$("#dateafter,#datebefore").attr('disabled',true);
            $("#dateafter,#datebefore").attr('value', 'N/A');*/

            var currentTime = new Date();
            var day = currentTime.getDate();
            if (day < 10){
            day = "0" + day;
            }
            var month = currentTime.getMonth() + 1;
            if (month < 10){
            month = "0" + month;
            }
            var year = currentTime.getFullYear();
            var now = day + "/" + month + "/" + year;
            $('#datepickerto').attr('value', now);

            $('.enable_date').click(function () {
            if($(this).attr('checked')){
              $(this).next().removeAttr('disabled');
              $(this).next().attr('value', now);  
            }else{
              $(this).next().attr('disabled',true);
              $(this).next().attr('value', 'N/A');
            }

            });
        });
        </script>
     
</head>

<body>
<?php include "../_inc/header.php"; ?>
<h2 class="enrolments">Group &amp; Enrolment Management: Edit Group "<?php echo $disp_name; ?>"</h2>
<div class="links">
    <p><a href="<?php echo $CFG->wwwroot ?>"><?php echo $SITE->fullname; ?></a> &raquo; <a href="<?php echo $linkBackToEnrolments ?>">Group &amp; Enrolment Management</a> &raquo; Edit Group "<?php echo $groupname; ?>"</p>
</div>

   

<?php

	if(isset($_SESSION['permission:editgroup'])){
    	
		
		if(isset($update_message)){ echo $update_message; }
		
 
        
        
		?>
    	<?php include('../_inc/inc.inner-group-admin.php');  ?>
        
        
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
              <label>Started after:</label>
              <input type="checkbox" class="enable_date" <?php echo $dateafter_chkd ?> />
              <input type="text" id="dateafter" name="dateafter" <?php echo $dateafter_text; ?> class='cb' />
              <label>Started before:</label>
              <input type="checkbox" class="enable_date" <?php echo $datebefore_chkd ?>/>
              <input type="text" id="datebefore" name="datebefore" <?php echo $datebefore_text; ?> class='cb' />
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
