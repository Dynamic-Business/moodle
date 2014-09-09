<?php
    
    require_once('../config.php');
    require_once('../_lib/form_lib.php');
    require_once('../_lib/sql_lib.php');
    

?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Moodle Reports</title>
    <?php echo $OUTPUT->standard_head_html() ?>
    <link href="../_css/styles.css" rel="stylesheet" type="text/css" />
    <style type="text/css" title="currentStyle">
            @import "../_datatables/media/css/demo_page.css";
            @import "../_datatables/media/css/demo_table_jui.css";
            @import "../_datatables/themes/smoothness/jquery-ui-1.8.4.custom.css";
    </style>
    <!--[if IE]>
        <link href="../_css/iefixes.css" rel="stylesheet" type="text/css" />
    <![endif]--> 
    <!--[if IE 9]>
        <link href="../_css/ie9fixes.css" rel="stylesheet" type="text/css" />
    <![endif]--> 
    <link type="text/css" href="../_css/ui-lightness/jquery-ui-1.8.4.custom.css" rel="stylesheet" />
        
    <script type="text/javascript" src="../_js/jquery-1.4.2.min.js"></script>
    <script type="text/javascript" src="../_js/jquery.validate.min.js"></script>
    <script type="text/javascript" src="../_js/jquery-ui-1.8.4.custom.min.js"></script>
    <script type="text/javascript" src="../_js/custom.js"></script>
    <script type="text/javascript" src="../_js/custom.logins.js"></script>
    

</head>

<body>
<?php include "../_inc/header.php"; ?>


<h2 class="report-header">Reports: User Login Report</h2>
<div class="links">
    <p><a href="<?php echo $CFG->wwwroot ?>"><?php echo $SITE->fullname; ?></a> &raquo; <a href="<?php echo $linkBackToReports ?>">Reports</a> &raquo; User Login Report </p>
</div>


<?php

if (isadmin()) { ?>

    <!--<h3>Report by Profile Data</h3>-->
    
     <p class="description">Select the date range below to show all user logins between the selected dates <i>or</i> show all users have not logged in to the system.</p>
     
    <form action="<?php echo $CFG->wwwroot . "/admin/dynamic_admin/dynamic_reports/report.php?type=byLogins";  ?>" method="POST" id="styled-form">
    <div class="form-section-builder">      
            <label>Report Type:</label><div class="checkbox-group-outer"><div class="radio-holder" ><input type="radio" name="logintype" value="logins" checked=true class='logins-rb' />User logins<input type="radio" name="logintype" value="nologins" class='no-logins-rb' />No logon</div></div>
    </div>
    <div class="form-section-builder">  
            <label>Date from:</label>
            <input type="text" id="datepickerfrom" name="datepickerfrom" value='<?php echo date("d/m/Y"); ?>' /><br />
            <label>Date to:</label>
            <input type="text" id="datepickerto" name="datepickerto" value='<?php echo date("d/m/Y"); ?>' />
     </div>
     <div class="form-section-builder last"> 
            <input type="submit" value="Submit" />
     </div>
      </form>

     
     <!-- Search by name -->  
    
<?php }else{ ?>
        <p class="no-permission">You do not have the correct permissions to view this page</p>
<?php   } ?> 
<?php
    
    echo $footer;
    
?>

</body>
</html>
