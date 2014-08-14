<!-- COPIED FROM A THEME TO SHOW HEADER -->
	<div id="page-header" class="clearfix">
		<div id="page-header-wrapper">


		    	<h1 class="headermain"><?php echo $PAGE->heading ?>&nbsp;</h1>
    		    <div class="headermenu">
        			<?php
            			echo $OUTPUT->login_info();
		           		if (!empty($PAGE->layout_options['langmenu'])) {
		        	       	echo $OUTPUT->lang_menu();
			    	    }
    			       	echo $PAGE->headingmenu
        			?>
	        	</div>
	    </div>
    </div>
    <!-- END -->