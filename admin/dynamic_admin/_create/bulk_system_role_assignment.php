<?php

/*
	- Collate list on an existing Moodle by going to Bulk User Actions > Download
    - This script reads a csv of 'username','role shortname' and assigns them the role at system level.
	- The username and role must exist within the system for this to work.
	- csv file must be named 'users.csv.' and stored on same level as this script (although this can be changed in the config below)
	- Built for Moodle 2.7
	- Can delete all role assignments that were done through thsi script with DELETE FROM mdl_role_assignments WHERE component = 'bulk-assign-script'
    - Run through the browser

    /*

    Script to pull return all headteamcoaches

    SELECT u.username,r.shortname 
    FROM next20.mdl_role_assignments ra
    JOIN mdl_user u ON u.id = ra.userid
    JOIN mdl_role r ON r.id = ra.roleid
    WHERE roleid = 102 AND u.deleted = 0


    */

    define('CLI_SCRIPT', true);	
	$system_contextid = 1;
	$system_contextlevel = 10;
	$csv_file = 'users.csv';

	require_once(dirname(__FILE__) . '/../../../config.php'); //main moodle config
	require_once(dirname(__FILE__) . '/../config.php'); //plugin config
	require_once(dirname(__FILE__) . '/../../../lib/csvlib.class.php'); 
    require_once(dirname(__FILE__) . '/../../../user/profile/lib.php'); 

    $iid = csv_import_reader::get_new_iid('uploaduser');
    $cir = new csv_import_reader($iid, 'uploaduser');
	
	$content = my_file_get_contents($csv_file); //path to file declared above
	$content = iconv('ASCII', 'UTF-8//IGNORE', $content); //Converts ASCII to UTF-8
	
	//trim utf-8 bom
	$textlib = new textlib();
	$content = $textlib->trim_utf8_bom($content);
	// Fix mac/dos newlines
	$content = preg_replace('!\r\n?!',"\n",$content);
	$fp = fopen($csv_file, "w");
	fwrite($fp,$content);
	fclose($fp);
	
    $readcount = $cir->load_csv_content($content, 'UTF-8', 'comma', NULL);
    unset($content);
    if ($readcount === false) {
		echo "Error: File error";
    } else if ($readcount == 0) {
		echo "Error: File is empty<br>";
    }

    if (!$columns = $cir->get_columns()) {
		echo "Error reading temporary file<br>";
	}

	// init csv import helper
    $cir->init();
    $linenum = 1; //column header is first line

    //final output variables
    $skipped = 0;
    $success = 0;

    while ($line = $cir->next()) {
        $linenum++;
        $user = new object();
       
        // add fields to user object
        foreach ($line as $key => $value) {
        	$key = $columns[$key];
        	if($key == 'username' || $key == 'role'){
        		$user->$key = $value;
        	}
        }

        //get userid based on username
        // $user->id = $DB->get_field('user', 'id', array('username'=>$user->username));
        // echo "userid: " . $userid . "<br>";die;

        if(!$user->id = $DB->get_field('user', 'id', array('username'=>$user->username))){
        	echo "Skipped: ".$user->username . " does not exist.<br>";
        	$skipped++;
        	continue;
        }else{

        }

        //Get the roleid of role from mdl_role
        if($result = $DB->get_record('role', array('shortname'=>$user->role))){
        	$roleid = $result->id;
        	//Can this role be assigned at system level? Check mdl_role_context_levels
        	if ($DB->record_exists('role_context_levels',array('roleid'=>$roleid,'contextlevel'=>$system_contextlevel))) { 
        		
        		//Has the user already been assigned this role under this context within role_assignments
        		if (!$DB->record_exists('role_assignments',array('roleid'=>$roleid,'contextid'=>$system_contextid,'userid'=>$user->id))) { 
        			// Assign user to role
        			$sqlInsert = "
						INSERT IGNORE INTO mdl_role_assignments (roleid,contextid,userid,timemodified,modifierid,component) VALUES  ({$roleid},{$system_contextid},{$user->id},UNIX_TIMESTAMP(NOW()),2,'bulk-assign-script')
					";

					//echo $sql1 . "<br>";
					$result1 = $DB->execute($sqlInsert) or die(mysql_error());
					if($result1){
						$success++;
						echo "Success: " . $user->username . " has been assigned the role of " . $user->role . "<br>";
					}else{
						echo "Error: Could not assign ".$user->username . " to role " . $user->role . ".<br>";
					}

        			//code here.
        		}else{
        			$skipped++;
        			echo "Skipped: ".$user->username . " is already assigned the role of " . $user->role . "<br>";
        		}
        	}else{
        		$skipped++;
        		echo "Skipped: ".$user->role." role cannot be assigned at system level.<br>";
        	}

        }else{
        	$skipped++;
        	echo "Skipped: ".$user->role." role doesn't exist. Please check the role shortname within Moodle.<br>";
        }
    }

    //close csv file reader
    $cir->close();
    $cir->cleanup(true);

    echo "<br>===== Script complete =====<br>";
    echo $skipped . " users skipped<br>";
    echo $success . " users successfully assigned<br>";

    //Functions
    function my_file_get_contents($filename, $use_include_path = 0) {
	    /// Returns the file as one big long string
		$data = "";
	    $file = @fopen($filename, "rb", $use_include_path);
	    if ($file) {
	        while (!feof($file)) {
	            $data .= fread($file, 1024);
	        }
	        fclose($file);
	    }
	    return $data;
	}

    

?>
