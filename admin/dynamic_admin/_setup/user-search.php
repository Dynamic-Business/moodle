<?php

/*

2 changes to be made inside admin/user.php to create extra column in user table with a link that links directly to the user report. 
The lines commented out below are just to show the code change - don't actually comment out these lines in the code.

*/


/*LINE 210 (approx).*/

//$table = new html_table();
$table->head = array ($fullnamedisplay, $email, $city, $country, $lastaccess, "", "", "",""); //DYNAMIC - extra empty column here
//$table->align = array ("left", "left", "left", "left", "left", "center", "center", "center");
	
	
/*LINE 275*/

/* $table->data[] = array ("<a href=\"../user/view.php?id=$user->id&amp;course=$site->id\">$fullname</a>",
                                "$user->email",
                                "$user->city",
                                "$user->country",
                                $strlastaccess,
                                $editbutton,
                                $deletebutton,
                                $confirmbutton,*/
								"<a href='dynamic_admin/dynamic_reports/report.php?type=byUser&userids=" .$user->id . "'>Individual Report</a>"); //DYNAMIC - link here
      /*  }*/
	
	
		
?>
        