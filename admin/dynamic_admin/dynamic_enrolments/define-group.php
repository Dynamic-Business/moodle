<?php

	require_once('../config.php');
	require_once('../_lib/form_lib.php');
	require_once($CFG->libdir . '/adminlib.php');
	$function = "define-group";
	//$groupId = $_GET['id'];
	$groupId = filter_var( $_GET['id'], FILTER_SANITIZE_NUMBER_INT);
	$row = $DB->get_record_sql("SELECT name FROM mdl_dynamic_group WHERE id=\"" . $groupId ."\"");
	$groupname = $row->name;
	//
	if (isset($_GET['action'])){$action = filter_var( $_GET['action'], FILTER_SANITIZE_STRING);} else{$action = "";}
	//
	$noOfFields = count($reportAdditionalIds);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Moodle Enrolments - Define Group <?php echo $region; ?> XX</title>
		<?php echo $OUTPUT->standard_head_html() ;?>

        <link href="../_css/styles.css" rel="stylesheet" type="text/css" />
         <!--[if IE 7]>
        	 <link href="../_css/ie7fixes.css" rel="stylesheet" type="text/css" />
		<![endif]--> 
        <style type="text/css" title="currentStyle">
                @import "../_datatables/media/css/demo_page.css";
                @import "../_datatables/media/css/demo_table_jui.css";
                @import "../_datatables/themes/smoothness/jquery-ui-1.8.4.custom.css";
        </style>
        
		<script type="text/javascript" language="javascript" src="../_datatables/media/js/jquery.js"></script>
		<script type="text/javascript" language="javascript" src="../_datatables/media/js/jquery.dataTables.js"></script>
		<script type="text/javascript" language="javascript" src="../_js/jquery.qtip-1.0.0-rc3.min.js"></script> 
		<script type="text/javascript" charset="utf-8">
			$(document).ready(function() {
				oTable = $('#styled-table').dataTable({
					"bJQueryUI": true,
					"sPaginationType": "full_numbers",
					"bRetrieve": true
				});
			} );

			$(document).ready(function(){
				$("input:checkbox").removeAttr('disabled'); //re-enable after loading
				$("input[type='checkbox']").change(function() {
					var data 	= $(this).val();
					var chkd 	= $(this).is(':checked');
					var field = $(this).attr("name");
					var groupid = <?php echo $groupId;  ?>;
					//alert("data:" + data + "  | chkld:" + chkd+ "  | groupid:" + groupid+ "  | field:" + field);
					if(chkd){
						chkdval = 1;
					}else{
						chkdval = 0;
					}

					$.ajaxSetup ({cache: false});
					
					var loadUrl = "../_ajax/ajax.group-define.php";
					ref = $(this);

					$('input[type=checkbox]').attr('disabled','true'); //disable any from being pressed whilst loading
					$.ajax({
				        url : loadUrl,
				        type : 'POST',
				        data : {data: data, chkd:chkdval, field:field, groupid:groupid},
				        dataType : 'json',
				        success : function (result) {
				        	$("input:checkbox").removeAttr('disabled'); //re-enable after loading
				          	ref.parent().next().next().text(result.selected + " selected");
				          	ref.parent().next().next().animate({opacity: 0,marginRight: "+=20px" }, 0 );
				          	ref.parent().next().next().animate({opacity: 1, marginRight: "-=20px"}, 400 );
				        },
				        error : function () { 
				        	alert("Error when trying to update status. please report this to the administrator.");
				        	$("input:checkbox").removeAttr('disabled'); //re-enable after loading
				        ;} 
				    });

				});	

				//Toggle for Expand
				$(".expand").toggle(function(){
				    $(this).parent().animate({"height":"+=120px"},200);
				    $(this).parent().children("DIV").show();
				    $(this).parent().children(".expand").html("Minimise &#9650;");
				},function(){
				   $(this).parent().animate({"height":"-=120px"},200);
				    $(this).parent().children("DIV").hide();
				    $(this).parent().children(".expand").html("Expand &#9660;");
				});

				$(".checkbox-group-outer").animate({"height":"-=120px"},0);
				$(".checkbox-group").hide();

				//Tooltip code for help icon
				$('a.help').qtip({
					content: 'Occasionally you may see text highlighted in red. This occurs when there are no users associated with the data. Simply uncheck the box to remove the defintion. The data will no longer be shown when you next visit this page.' ,
					style: { name: 'light'} 
			   	});



			});
		</script>

</head>

<?php 
//debug
//echo $region;
//echo $jobrole;

?>

<body class="enrolments">
<?php include "../_inc/header.php"; ?>
<h2 class="enrolments">Group &amp; Enrolment Management: Define Group "<?php echo $groupname; ?>"</h2>
<div class="links">
    <p><a href="<?php echo $CFG->wwwroot ?>">Moodle</a> &raquo; <a href="<?php echo $linkBackToEnrolments ?>">Group &amp; Enrolment Management</a> &raquo; Define Group "<?php echo $groupname; ?>"</p>
</div>

<!--<p class="description">
    This page allows you to add and remove user properties to a Group. The 'Property Name' field is populated from user property names already setup within Moodle. The 'Property Value' will be any possible value for the properties from the drop down list. The values you enter here must match exactly to the field values in a users profile.
</p>-->

<?php


if(isset($_SESSION['permission:definegroup'])){

	
	include('../_inc/inc.inner-group-admin.php');  ?>
    
    <div class="form-area">
        <h3>Update Group Definition</h3>
        <a href="#" class='help'>Help</a>
        <form action="<?php echo htmlentities($_SERVER['PHP_SELF'])  . "?action=add&id=" . $groupId; ?>" method="POST" id="styled-form">
        
        	<?php
			$noOfFields = count($reportAdditionalIds);
        	for ($i=0;$i<$noOfFields;$i++){
        		if($reportAdditionalColumns[$i] != "datestarted"){
					echo "<div class='form-section'>";
					echo "<label>" . $reportAdditionalColumns[$i] . "</label>";
					//build_data_formlist_define($reportAdditionalColumns[$i]);
					build_data_formcb($reportAdditionalColumns[$i],TRUE);
					echo "</div>"; 
				}
			}
			?>
           
        </form>
    </div>
    
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
