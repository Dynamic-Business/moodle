<?php  

define('CLI_SCRIPT', true);

/*
	FOR MOODLE 2.0
	Trial and error has produduced this file from 1.9 so will not be as clean as it could be.
	Hardcoded 'Manual' auth plugin so that is the only type of authentication allowed for thit type of upload
	
	Setup
	1. Replace settings below if required. May need to edit csv path.
	2. Create log file at: C:\Program Files\Zend\Apache2\htdocs\coopimport\_log\log.txt
	3. Copy this script to admin folder of moodle installation
	4. Change variable below to FALSE - don't want it running remotely on live site.
	5. Create a scheduled task for (or similar). Ensure its enabled and run as administrator:
		"C:\Program Files\Zend\ZendServer\bin\php.exe" -f "C:\Program Files\Zend\Apache2\htdocs\midcounties\admin\dynamic_autouploaduser.php"
	6. Make sure that the databases is getting backed up every night in case of upload error.
	
	Possible further development
	1. Flag an error if the csv file contains preceeding zeros. EXCEL issue with saving idnumbers including zeros
	2. May need to write more specific erros if lines fail.
	3. Remove facility for their admins to add user through interface.	

*/

//$datetime = new DateTime();
//writeToLog("script started: " . strftime('%c'));

/* config ----------------------------------------------------------------------------------------------*/

//1. Can this script be run through a webpage / remotely?
	$runRemote = TRUE;

//2. change user profile fields from around line 200.

//3. Change these settings depending on Server
	//--apache local
	//$filename = "C:/xampp/htdocs/userimport-anchor/anchor-users.csv";
	//$logfile = "C:/xampp/htdocs/userimport-anchor/_log/log.txt";
		
	//--Windows Raackspace
	$filename = "C:\hr-data\users\users.csv";
	$logfile = "C:\hr-data\_log\log.txt";

//4. Change these settings for different types of auto upload
	$UploadType = 2; // 0=Add new only | 1=Add all append counterto username if needed | 2=Add new and update existing | 3=Update existing only
	$NewUserPassword = 0; // 0= field required | 1=create password if needed
	$ExistingUserDetails = 1; // 0=No Changes | 1=Overide with file | 2=Overide with file and defaults | 3=Fill in missing from fiel and defaults
	$ExistingUserPassword =0; // 0=No Changes | 1=Update (Should be set to 0)
	//can we add in here $ForcePasswordChange=2; //0=users having weak password | 1=None | 2=All   -LW
	$forcechangepassword=2;
	$AllowRenames =0; // 0=no | 1=yes
	$AllowDeletes =1; // 0=no | 1=yes
	$PreventEmailDuplicates =0; // 0=no | 1=yes
	
//5. May need to change path for different Servers

    //These paths work on both Windows and Linux (scheduled tasks / Cron Jobs)
    /*require_once(dirname(__FILE__) . '/../../config.php'); //main moodle config
    require_once(dirname(__FILE__) . '/../../lib/adminlib.php'); 
    require_once(dirname(__FILE__) . '/../../lib/csvlib.class.php'); 
    require_once(dirname(__FILE__) . '/../../user/profile/lib.php'); 
    require_once(dirname(__FILE__) . '/../tool/uploaduser/user_form.php'); 
    require_once(dirname(__FILE__) . '/../../lib/phpmailer/class.phpmailer.php');*/

    require_once(dirname(__FILE__) . '/../config.php'); //main moodle config
    require_once(dirname(__FILE__) . '/../lib/adminlib.php'); 
    require_once(dirname(__FILE__) . '/../lib/csvlib.class.php'); 
    require_once(dirname(__FILE__) . '/../user/profile/lib.php'); 
    require_once(dirname(__FILE__) . '/tool/uploaduser/user_form.php'); 
    require_once(dirname(__FILE__) . '/../lib/phpmailer/class.phpmailer.php');



define('UU_ADDNEW', 0);
define('UU_ADDINC', 1);
define('UU_ADD_UPDATE', 2);
define('UU_UPDATE', 3);

$struserrenamed             = get_string('userrenamed', 'admin');
$strusernotrenamedexists    = get_string('usernotrenamedexists', 'error');
$strusernotrenamedmissing   = get_string('usernotrenamedmissing', 'error');
$strusernotrenamedoff       = get_string('usernotrenamedoff', 'error');
$strusernotrenamedadmin     = get_string('usernotrenamedadmin', 'error');
$struserupdated             = get_string('useraccountupdated', 'admin');
$strusernotupdated          = get_string('usernotupdatederror', 'error');
$strusernotupdatednotexists = get_string('usernotupdatednotexists', 'error');
$strusernotupdatedadmin     = get_string('usernotupdatedadmin', 'error');
$struseruptodate            = get_string('useraccountuptodate', 'admin');
$struseradded               = get_string('newuser');
$strusernotadded            = get_string('usernotaddedregistered', 'error');
$strusernotaddederror       = get_string('usernotaddederror', 'error');
$struserdeleted             = get_string('userdeleted', 'admin');
$strusernotdeletederror     = get_string('usernotdeletederror', 'error');
$strusernotdeletedmissing   = get_string('usernotdeletedmissing', 'error');
$strusernotdeletedoff       = get_string('usernotdeletedoff', 'error');
$strusernotdeletedadmin     = get_string('usernotdeletedadmin', 'error');
$strcannotassignrole        = get_string('cannotassignrole', 'error');
$struserauthunsupported     = get_string('userauthunsupported', 'error');
$stremailduplicate          = get_string('useremailduplicate', 'error');
$strinvalidpasswordpolicy   = get_string('invalidpasswordpolicy', 'error');
$errorstr                   = get_string('error');

@set_time_limit(20000); 
@raise_memory_limit('256M');
if (function_exists('apache_child_terminate')) {
    // if we are running from Apache, give httpd a hint that
    // it can recycle the process after it's done. Apache's
    // memory management is truly awful but we can help it.
    @apache_child_terminate();
}


//Remote Addr is not set when running through the system (i.e. scheduled tasks)
if (isset($_SERVER['REMOTE_ADDR']) && !$runRemote) {
	writeToLog("Attempt to run through web page/remote address. Change settings in script.",true);
	exit;
}

// array of all valid fields for validation
$STD_FIELDS = array('id', 'firstname', 'lastname', 'username', 'email', 
        'city', 'country', 'lang', 'auth', 'timezone', 'mailformat', 
        'maildisplay', 'maildigest', 'htmleditor', 'ajax', 'autosubscribe', 
        'mnethostid', 'institution', 'department', 'idnumber', 'skype', 
        'msn', 'aim', 'yahoo', 'icq', 'phone1', 'phone2', 'address', 
        'url', 'description', 'oldusername', 'emailstop', 'deleted',  
        'password');

$PRF_FIELDS = array();

//if ($prof_fields = get_records('user_info_field')) {

if ($prof_fields = $DB->get_records('user_info_field')) {
    foreach ($prof_fields as $prof_field) {
        $PRF_FIELDS[] = 'profile_field_'.$prof_field->shortname;
    }
    unset($prof_fields);
}


//Id is only created when ready for the actual upload. Therefore if empty, go to stage 1 or 2.
if (empty($iid)) {

    //require_login();
    $PAGE->set_context(context_system::instance());
    $PAGE->set_pagelayout('embedded');
    $PAGE->set_url('/admin/dynamic_autouploaduser.php');
    echo $OUTPUT->standard_head_html(); 


    $mform = new admin_uploaduser_form1();
		
	//New hardcoded class to bypass the $mform->get_data() method. These are vals received from first form
	$formdata2 = new stdClass();
	$formdata2->MAX_FILE_SIZE = '20971520';
	$formdata2->delimiter_name = 'comma';
	$formdata2->encoding = 'UTF-8';
	$formdata2->preview_rows = 10;
	$formdata2->submitbutton = 'Upload users';
	
	//IF the first form has been completed then go to stage 2 (bit with preview)
    //if ($formdata = $mform->get_data()) {
	if ($formdata = $formdata2) {

        $iid = csv_import_reader::get_new_iid('uploaduser');
        $cir = new csv_import_reader($iid, 'uploaduser');
		
        //$content = $mform->get_file_content('userfile');
		$content = my_file_get_contents($filename); //path to file declared above
		
		$content =  iconv('ASCII', 'UTF-8//IGNORE', $content); //Converts ASCII to UTF-8 (new code 06.11.12 following encoding issues)
		
		//trim utf-8 bom
		$textlib = new textlib();
		$content = $textlib->trim_utf8_bom($content);
		//Fix mac/dos newlines
		$content = preg_replace('!\r\n?!',"\n",$content);
		$fp = fopen($filename, "w");
		fwrite($fp,$content);
		fclose($fp);
		
        $readcount = $cir->load_csv_content($content, $formdata->encoding, $formdata->delimiter_name, 'validate_user_upload_columns');
        unset($content);
        if ($readcount === false) {
			writeToLog($cir->get_error(),true);
            error($cir->get_error(), $returnurl);
        } else if ($readcount == 0) {
			writeToLog("Empty CSV File",true);
            //print_error('csvemptyfile', 'error', $returnurl);
        }
        // continue to form2
	
	//ELSE go back to stage 1
    } else {

        die;
    }
//ELSE create a new csv import reader ready for upload and continue
} else {
    $cir = new csv_import_reader($iid, 'uploaduser');
}

if (!$columns = $cir->get_columns()) {
	writeToLog("Error reading temporary file",true);

}
//$mform = new admin_uploaduser_form2(null, $columns);
// get initial data from form1
$previewrows = "";
//$mform->set_data(array('iid'=>$iid, 'previewrows'=>$previewrows, 'readcount'=>$readcount));

//If a file has been uploaded, then process it
//If form has been cancelled

//New stdClass to bypass stage 2 and gop straight to the file upload.These are the default values

$formdata3 = new stdClass();
$formdata3->MAX_FILE_SIZE = '20971520';
$formdata3->uutype = $UploadType;
$formdata3->uupasswordnew = $NewUserPassword;
$formdata3->uuupdatetype = $ExistingUserDetails;
$formdata3->uupasswordold =$ExistingUserPassword;
$formdata3->uuallowrenames =$AllowRenames;
$formdata3->uuallowdeletes =$AllowDeletes;
$formdata3->uunoemailduplicates =$PreventEmailDuplicates;
$formdata3->uubulk =0;
$formdata3->auth ='manual';
$formdata3->mform_showadvanced_last =0;
$formdata3->maildisplay =2;
$formdata3->emailstop =0;
$formdata3->mailformat =1;
$formdata3->maildigest =0;
$formdata3->autosubscribe =1;
$formdata3->ajax =1;
$formdata3->city ='Unknown';
$formdata3->country ='GB';
$formdata3->timezone =99;
$formdata3->lang ='en_utf8';
$formdata3->description = '';
$formdata3->url = '';
$formdata3->institution = '';
$formdata3->department = '';
$formdata3->phone1 = '';
$formdata3->phone2 = '';
$formdata3->address = '';

//Manually put the profile field names here
$formdata3->profile_field_datestarted =	'';
$formdata3->profile_field_storedetails = '';
$formdata3->profile_field_jobcode = '';
$formdata3->profile_field_jobgrade = '';
$formdata3->profile_field_dept = '';
$formdata3->profile_field_contractedhours = '';
$formdata3->profile_field_contractend = '';
$formdata3->profile_field_isover18 = 0;
$formdata3->profile_field_isover21 = 0;
$formdata3->profile_field_leaverindicator = 0;
$formdata3->profile_field_teamcoach = 0;
$formdata3->profile_field_headteamcoach = 0;


/*$formdata3->profile_field_jobrole =	'';
$formdata3->profile_field_region = '';*/

//--
$formdata3->iid =$iid;
$formdata3->previewrows =10;
$formdata3->readcount =1;
$formdata3->submitbutton ='Upload users';

if ($formdata = $formdata3) { // no magic quotes here!!!
//if ($formdata = $mform->get_data(false)) { // no magic quotes here!!!
	$timestart = strftime('%c');
	
    $optype = $formdata->uutype;

    $createpasswords   = (!empty($formdata->uupasswordnew) and $optype != UU_UPDATE);
    $updatepasswords   = (!empty($formdata->uupasswordold)  and $optype != UU_ADDNEW and $optype != UU_ADDINC);
    $allowrenames      = (!empty($formdata->uuallowrenames) and $optype != UU_ADDNEW and $optype != UU_ADDINC);
    $allowdeletes      = (!empty($formdata->uuallowdeletes) and $optype != UU_ADDNEW and $optype != UU_ADDINC);
    $updatetype        = isset($formdata->uuupdatetype) ? $formdata->uuupdatetype : 0;
    $bulk              = $formdata->uubulk;
    $noemailduplicates = $formdata->uunoemailduplicates;

    // verification moved to two places: after upload and into form2
    $usersnew     = 0;
    $usersupdated = 0;
    $userserrors  = 0;
    $deletes      = 0;
    $deleteerrors = 0;
    $renames      = 0;
    $renameerrors = 0;
    $usersskipped = 0;
    $weakpasswords = 0;

    // caches
    $ccache    = array(); // course cache - do not fetch all courses here, we  will not probably use them all anyway!
    $rolecache = array(); // roles lookup cache

	$allowedauths   = uu_allowed_auths();
    $allowedauths   = array_keys($allowedauths);
    $availableauths = get_list_of_plugins('auth');
	//$allowedauths = array("manual");
	//$availableauths = array("manual");

    $allowedroles = uu_allowed_roles(true);
    foreach ($allowedroles as $rid=>$rname) {
        $rolecache[$rid] = new object();
        $rolecache[$rid]->id = $rid;
        $rolecache[$rid]->name = $rname;
        if (!is_numeric($rname)) { // only non-numeric shornames are supported!!!
            $rolecache[$rname] = new object();
            $rolecache[$rname]->id = $rid;
            $rolecache[$rname]->name = $rname;
        }
    }
    unset($allowedroles);

    // clear bulk selection
    if ($bulk) {
        $SESSION->bulk_users = array();
    }

    // init csv import helper
    $cir->init();
    $linenum = 1; //column header is first line

    // init upload progress tracker
    $upt = new uu_progress_tracker();
    $upt->start(); // start table

    while ($line = $cir->next()) {
        $upt->flush();
        $linenum++;

        $upt->track('line', $linenum);

        //$forcechangepassword = false;

        $user = new object();
        // by default, use the local mnet id (this may be changed in the file)
        $user->mnethostid = $CFG->mnet_localhost_id;
        // add fields to user object
        foreach ($line as $key => $value) {
            if ($value !== '') {
                $key = $columns[$key];
                // password is special field
                if ($key == 'password') {
                    if ($value !== '') {
                        $user->password = hash_internal_user_password($value);
                        if (!empty($CFG->passwordpolicy) and !check_password_policy($value, $errmsg)) {
                            $forcechangepassword = true;
                            $weakpasswords++;
                        }
                    }
                } else {
                    $user->$key = $value;
                    if (in_array($key, $upt->columns)) {
                        $upt->track($key, $value);
                    }
                }
            }
        }

        // get username, first/last name now - we need them in templates!!
        if ($optype == UU_UPDATE) {
            // when updating only username is required
            if (!isset($user->username)) {
                $upt->track('status', get_string('missingfield', 'error', 'username'), 'error');
                $upt->track('username', $errorstr, 'error');
                $userserrors++;
                continue;
            }

        } else {
            $error = false;
            // when all other ops need firstname and lastname
            if (!isset($user->firstname) or $user->firstname === '') {
                $upt->track('status', get_string('missingfield', 'error', 'firstname'), 'error');
                $upt->track('firstname', $errorstr, 'error');
                $error = true;
            }
            if (!isset($user->lastname) or $user->lastname === '') {
                $upt->track('status', get_string('missingfield', 'error', 'lastname'), 'error');
                $upt->track('lastname', $errorstr, 'error');
                $error = true;
            }
            if ($error) {
                $userserrors++;
                continue;
            }
            // we require username too - we might use template for it though
			
            if (!isset($user->username)) {
                if (!isset($formdata->username) or $formdata->username === '') {
                    $upt->track('status', get_string('missingfield', 'error', 'username'), 'error');
                    $upt->track('username', $errorstr, 'error');
                    $userserrors++;
                    continue;
                }  else {
                    $user->username = process_template($formdata->username, $user);
                    $upt->track('username', $user->username);
                }
            }
        }
		//$firephp->log($user->username,'username');
        // normalize username
        $user->username = $textlib->strtolower($user->username);
        /*if (empty($CFG->extendedusernamechars)) {
            $user->username = preg_replace('[^(-\.[:alnum:])]', '', $user->username);
        }*/
		
        if (empty($user->username)) {
            $upt->track('status', get_string('missingfield', 'error', 'username'), 'error');
            $upt->track('username', $errorstr, 'error');
            $userserrors++;
            continue;
        }

        if ($existinguser = $DB->get_record('user',  array('username'=>addslashes($user->username),'mnethostid'=>$user->mnethostid))) {
            $upt->track('id', $existinguser->id, 'normal', false);
        }
		
        // find out in username incrementing required
        if ($existinguser and $optype == UU_ADDINC) {
            $oldusername = $user->username;
            $user->username = increment_username($user->username, $user->mnethostid);
            $upt->track('username', '', 'normal', false); // clear previous
            $upt->track('username', $oldusername.'-->'.$user->username, 'info');
            $existinguser = false;
        }

        // add default values for remaining fields
        foreach ($STD_FIELDS as $field) {
            if (isset($user->$field)) {
                continue;
            }
            // all validation moved to form2
            if (isset($formdata->$field)) {
                // process templates
                $user->$field = process_template($formdata->$field, $user);
            }
        }
        foreach ($PRF_FIELDS as $field) {
            if (isset($user->$field)) {
                continue;
            }
            if (isset($formdata->$field)) {
                // process templates
                $user->$field = process_template($formdata->$field, $user);
            }
        }

        // delete user
        if (!empty($user->deleted)) {
            if (!$allowdeletes) {
                $usersskipped++;
                $upt->track('status', $strusernotdeletedoff, 'warning');
                continue;
            }
            if ($existinguser) {
                if (has_capability('moodle/site:doanything', $systemcontext, $existinguser->id)) {
                    $upt->track('status', $strusernotdeletedadmin, 'error');
                    $deleteerrors++;
                    continue;
                }
                if (delete_user($existinguser)) {
                    $upt->track('status', $struserdeleted);
                    $deletes++;
                } else {
                    $upt->track('status', $strusernotdeletederror, 'error');
                    $deleteerrors++;
                }
            } else {
                $upt->track('status', $strusernotdeletedmissing, 'error');
                $deleteerrors++;
            }
            continue;
        }
        // we do not need the deleted flag anymore
        unset($user->deleted);

        // renaming requested?
        if (!empty($user->oldusername) ) {
            $oldusername = $textlib->strtolower($user->oldusername);
            if (!$allowrenames) {
                $usersskipped++;
                $upt->track('status', $strusernotrenamedoff, 'warning');
                continue;
            }

            if ($existinguser) {
                $upt->track('status', $strusernotrenamedexists, 'error');
                $renameerrors++;
                continue;
            }

            if ($olduser = get_record('user', 'username', addslashes($oldusername), 'mnethostid', addslashes($user->mnethostid))) {
                $upt->track('id', $olduser->id, 'normal', false);
                //if (has_capability('moodle/site:doanything', $systemcontext, $olduser->id)) {
				 if (is_siteadmin($user->id)) {
                    $upt->track('status', $strusernotrenamedadmin, 'error');
                    $renameerrors++;
                    continue;
                }
                if (set_field('user', 'username', addslashes($user->username), 'id', $olduser->id)) {
                    $upt->track('username', '', 'normal', false); // clear previous
                    $upt->track('username', $oldusername.'-->'.$user->username, 'info');
                    $upt->track('status', $struserrenamed);
                    $renames++;
                } else {
                    $upt->track('status', $strusernotrenamedexists, 'error');
                    $renameerrors++;
                    continue;
                }
            } else {
                $upt->track('status', $strusernotrenamedmissing, 'error');
                $renameerrors++;
                continue;
            }
            $existinguser = $olduser;
            $existinguser->username = $user->username;
        }

        // can we process with update or insert?
        $skip = false;
        switch ($optype) {
            case UU_ADDNEW:
                if ($existinguser) {
                    $usersskipped++;
                    $upt->track('status', $strusernotadded, 'warning');
                    $skip = true;;
                }
                break;

            case UU_ADDINC:
                if ($existinguser) {
                    //this should not happen!
                    $upt->track('status', $strusernotaddederror, 'error');
                    $userserrors++;
                    continue;
                }
                break;

            case UU_ADD_UPDATE:
                break;

            case UU_UPDATE:
                if (!$existinguser) {
                    $usersskipped++;
                    $upt->track('status', $strusernotupdatednotexists, 'warning');
                    $skip = true;
                }
                break;
        }

        if ($skip) {
            continue;
        }

        if ($existinguser) {
            $user->id = $existinguser->id;

           //if (has_capability('moodle/site:doanything', $systemcontext, $user->id)) {
		   if (is_siteadmin($user->id)) {
                $upt->track('status', $strusernotupdatedadmin, 'error');
                $userserrors++;
                continue;
            }

            if (!$updatetype) {
                // no updates of existing data at all
            } else {
                $existinguser->timemodified = time();
                //load existing profile data
                profile_load_data($existinguser);

                $allowed = array();
                if ($updatetype == 1) {
                    $allowed = $columns;
                } else if ($updatetype == 2 or $updatetype == 3) {
                    $allowed = array_merge($STD_FIELDS, $PRF_FIELDS);
                }
                foreach ($allowed as $column) {
                    if ($column == 'username') {
                        continue;
                    }
                    if ($column == 'password') {
                        if (!$updatepasswords or $updatetype == 3) {
                            continue;
                        } else if (!empty($user->password)) {
                            $upt->track('password', get_string('updated'));
                            if ($forcechangepassword) {
                                set_user_preference('auth_forcepasswordchange', 1, $existinguser->id);
                            }
                        }
                    }
                    if ((array_key_exists($column, $existinguser) and array_key_exists($column, $user)) or in_array($column, $PRF_FIELDS)) {
                        if ($updatetype == 3 and $existinguser->$column !== '') {
                            //missing == non-empty only
                            continue;
                        }
                        if ($existinguser->$column !== $user->$column) {
                            if ($column == 'email') {
                                if ($DB->record_exists('user', array('email'=>addslashes($user->email)))) {
                                    if ($noemailduplicates) {
                                        $upt->track('email', $stremailduplicate, 'error');
                                        $upt->track('status', $strusernotupdated, 'error');
                                        $userserrors++;
                                        continue 2;
                                    } else {
                                        $upt->track('email', $stremailduplicate, 'warning');
                                    }
                                }
                            }
                            if ($column != 'password' and in_array($column, $upt->columns)) {
                                $upt->track($column, '', 'normal', false); // clear previous
                                $upt->track($column, $existinguser->$column.'-->'.$user->$column, 'info');
                            }
                            $existinguser->$column = $user->$column;
                        }
                    }
                }

                // do not update record if new auth plguin does not exist!
                if (!in_array($existinguser->auth, $allowedauths)) {
                    $upt->track('auth', get_string('userautherror', 'error', $existinguser->auth), 'error');
                    $upt->track('status', $strusernotupdated, 'error');
                    $userserrors++;
                    continue;
                } else if (!in_array($existinguser->auth, $allowedauths)) {
                    $upt->track('auth', $struserauthunsupported, 'warning');
                }

                if ($DB->update_record('user', $existinguser)) {
                    $upt->track('status', $struserupdated);
                    $usersupdated++;
                } else {
                    $upt->track('status', $strusernotupdated, 'error');
                    $userserrors++;
                    continue;
                }
                // save custom profile fields data from csv file
                profile_save_data($existinguser);
            }

            if ($bulk == 2 or $bulk == 3) {
                if (!in_array($user->id, $SESSION->bulk_users)) {
                    $SESSION->bulk_users[] = $user->id;
                }
            }

        } else {
            // save the user to the database
            $user->confirmed = 1;
            $user->timemodified = time();
            $user->timecreated = time();

            if (!$createpasswords and empty($user->password)) {
                $upt->track('password', get_string('missingfield', 'error', 'password'), 'error');
                $upt->track('status', $strusernotaddederror, 'error');
                $userserrors++;
                continue;
            }

            // do not insert record if new auth plguin does not exist!
            if (isset($user->auth)) {
                if (!in_array($user->auth, array('manual'))) {
                    $upt->track('auth', get_string('userautherror', 'error', $user->auth), 'error');
                    $upt->track('status', $strusernotaddederror, 'error');
                    $userserrors++;
                    continue;
                } else if (!in_array($user->auth, $allowedauths)) {
                    $upt->track('auth', $struserauthunsupported, 'warning');
                }
            }

            if ($DB->record_exists('user',array('email'=>addslashes($user->email)))) { //did say add_slashesrecursive
                if ($noemailduplicates) {
                    $upt->track('email', $stremailduplicate, 'error');
                    $upt->track('status', $strusernotaddederror, 'error');
                    $userserrors++;
                    continue;
                } else {
                    $upt->track('email', $stremailduplicate, 'warning');
                }
            }
			echo "USER:";
			print_r($user);
            if ($user->id = $DB->insert_record('user', $user)) {
                $info = ': ' . $user->username .' (ID = ' . $user->id . ')';
                $upt->track('status', $struseradded);
                $upt->track('id', $user->id, 'normal', false);
                $usersnew++;
                if ($createpasswords and empty($user->password)) {
                    // passwords will be created and sent out on cron
                    set_user_preference('create_password', 1, $user->id);
                    set_user_preference('auth_forcepasswordchange', 1, $user->id);
                    $upt->track('password', get_string('new'));
                }
                if ($forcechangepassword) {
                    set_user_preference('auth_forcepasswordchange', 1, $user->id);
                }
            } else {
                // Record not added -- possibly some other error
                $upt->track('status', $strusernotaddederror, 'error');
                $userserrors++;
                continue;
            }
            // save custom profile fields data
            profile_save_data($user);

            // make sure user context exists
            get_context_instance(CONTEXT_USER, $user->id);

            if ($bulk == 1 or $bulk == 3) {
                if (!in_array($user->id, $SESSION->bulk_users)) {
                    $SESSION->bulk_users[] = $user->id;
                }
            }
        }
	
    }
    $upt->flush();
    $upt->close(); // close table

    $cir->close();
    $cir->cleanup(true);
	$timeend = strftime('%c');

	$stringData = "Users Created:\t" . $usersnew . 
				  "\r\nUsers Updated:\t" . $usersupdated . 
				  "\r\nUsers Skipped:\t" . $usersskipped . 
				  "\r\nUser Errors:\t" . $userserrors . 
				  "\r\nUser Deletes:\t" . $deletes . 
				  "\r\n[Start: " . $timestart . "] [End: " . $timeend . "]";
	writeToLog ($stringData);
    die;
}


die;

/////////////////////////////////////
/// Utility functions and classes ///
/////////////////////////////////////

class uu_progress_tracker {
    var $_row;
    var $columns = array('status', 'line', 'id', 'username', 'firstname', 'lastname', 'email', 'password', 'auth', 'enrolments', 'deleted');

    function uu_progress_tracker() {
    }

    function start() {
        $ci = 0;
        echo '<table id="uuresults" class="generaltable boxaligncenter" summary="'.get_string('uploadusersresult', 'admin').'">';
        echo '<tr class="heading r0">';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('status').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('uucsvline', 'admin').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">ID</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('username').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('firstname').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('lastname').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('email').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('password').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('authentication').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('enrolments').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('delete').'</th>';
        echo '</tr>';
        $this->_row = null;
    }

    function flush() {
        if (empty($this->_row) or empty($this->_row['line']['normal'])) {
            $this->_row = array();
            foreach ($this->columns as $col) {
                $this->_row[$col] = array('normal'=>'', 'info'=>'', 'warning'=>'', 'error'=>'');
            }
            return;
        }
        $ci = 0;
        $ri = 1;
        echo '<tr class="r'.$ri++.'">';
        foreach ($this->_row as $field) {
            foreach ($field as $type=>$content) {
                if ($field[$type] !== '') {
                    $field[$type] = '<span class="uu'.$type.'">'.$field[$type].'</span>';
                } else {
                    unset($field[$type]);
                }
            }
            echo '<td class="cell c'.$ci++.'">';
            if (!empty($field)) {
                echo implode('<br />', $field);
            } else {
                echo '&nbsp;';
            }
            echo '</td>';
        }
        echo '</tr>';
        foreach ($this->columns as $col) {
            $this->_row[$col] = array('normal'=>'', 'info'=>'', 'warning'=>'', 'error'=>'');
        }
    }

    function track($col, $msg, $level='normal', $merge=true) {
        if (empty($this->_row)) {
            $this->flush(); //init arrays
        }
        if (!in_array($col, $this->columns)) {
            debugging('Incorrect column:'.$col);
            return;
        }
        if ($merge) {
            if ($this->_row[$col][$level] != '') {
                $this->_row[$col][$level] .='<br />';
            }
            $this->_row[$col][$level] .= s($msg);
        } else {
            $this->_row[$col][$level] = s($msg);
        }
    }

    function close() {
        echo '</table>';
    }
}

/**
 * Validation callback function - verified the column line of csv file.
 * Converts column names to lowercase too.
 */
function validate_user_upload_columns(&$columns) {
    global $STD_FIELDS, $PRF_FIELDS;

    if (count($columns) < 2) {
        return get_string('csvfewcolumns', 'error');
    }

    // test columns
    $processed = array();
    foreach ($columns as $key=>$unused) {
        $columns[$key] = strtolower($columns[$key]); // no unicode expected here, ignore case
        $field = $columns[$key];
        if (!in_array($field, $STD_FIELDS) && !in_array($field, $PRF_FIELDS) &&// if not a standard field and not an enrolment field, then we have an error
            !preg_match('/^course\d+$/', $field) && !preg_match('/^group\d+$/', $field) &&
            !preg_match('/^type\d+$/', $field) && !preg_match('/^role\d+$/', $field)) {
            return get_string('invalidfieldname', 'error', $field);
        }
        if (in_array($field, $processed)) {
            return get_string('csvcolumnduplicates', 'error');
        }
        $processed[] = $field;
    }
    return true;
}

/**
 * Increments username - increments trailing number or adds it if not present.
 * Varifies that the new username does not exist yet
 * @param string $username
 * @return incremented username which does not exist yet
 */
function increment_username($username, $mnethostid) {
    if (!preg_match_all('/(.*?)([0-9]+)$/', $username, $matches)) {
        $username = $username.'2';
    } else {
        $username = $matches[1][0].($matches[2][0]+1);
    }

    if (record_exists('user', 'username', addslashes($username), 'mnethostid', addslashes($mnethostid))) {
        return increment_username($username, $mnethostid);
    } else {
        return $username;
    }
}

/**
 * Check if default field contains templates and apply them.
 * @param string template - potential tempalte string
 * @param object user object- we need username, firstname and lastname
 * @return string field value
 */
function process_template($template, $user) {
    if (strpos($template, '%') === false) {
        return $template;
    }

    // very very ugly hack!
    global $template_globals;
    $template_globals = new object();
    $template_globals->username  = isset($user->username)  ? $user->username  : '';
    $template_globals->firstname = isset($user->firstname) ? $user->firstname : '';
    $template_globals->lastname  = isset($user->lastname)  ? $user->lastname  : '';

    $result = preg_replace_callback('/(?<!%)%([+-~])?(\d)*([flu])/', 'process_template_callback', $template);

    $template_globals = null;

    if (is_null($result)) {
        return $template; //error during regex processing??
    } else {
        return $result;
    }
}

/**
 * Internal callback function.
 */
function process_template_callback($block) {
    global $template_globals;
    $textlib = textlib_get_instance();
    $repl = $block[0];

    switch ($block[3]) {
        case 'u': $repl = $template_globals->username; break;
        case 'f': $repl = $template_globals->firstname; break;
        case 'l': $repl = $template_globals->lastname; break;
    }
    switch ($block[1]) {
        case '+': $repl = $textlib->strtoupper($repl); break;
        case '-': $repl = $textlib->strtolower($repl); break;
        case '~': $repl = $textlib->strtotitle($repl); break;
    }
    if (!empty($block[2])) {
        $repl = $textlib->substr($repl, 0 , $block[2]);
    }

    return $repl;
}

/**
 * Returns list of auth plugins that are enabled and known to work.
 */
function uu_allowed_auths() {
    global $CFG;

    // only following plugins are guaranteed to work properly
    // TODO: add support for more plguins in 2.0
    $whitelist = array('manual', 'nologin', 'none', 'email');
    $plugins = get_enabled_auth_plugins();
    $choices = array();
    foreach ($plugins as $plugin) {
        //$choices[$plugin] = auth_get_plugin_title ($plugin); //depracated
        $choices[$plugin] = get_string("pluginname", $plugin);
    }


    return $choices;
}

/**
 * Returns list of non administrator roles
 */
 
 //Removed due to Error Messages
function uu_allowed_roles($shortname=false) {
    global $CFG;

    $roles = get_all_roles();
    $choices = array();
    foreach($roles as $role) {
        if ($shortname) {
            $choices[$role->id] = $role->shortname;
        } else {
            $choices[$role->id] = format_string($role->name);
        }
    }
    // get rid of all admin roles
    if ($adminroles = get_roles_with_capability('moodle/site:doanything', CAP_ALLOW)) {
        foreach($adminroles as $adminrole) {
            unset($choices[$adminrole->id]);
        }
    }

    return $choices;
}

/* ----------------------------------------- */
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

function writeToLog($msg,$error=false){
	global $logfile;
	$fh = fopen($logfile, 'a') or die("can't open file");
	$stringData = date('l dS \of F Y h:i:s A') . "";
	if($error){
		$stringData .= "\r\nERROR : " . $msg;
	}else{
		$stringData .= "\r\n" . $msg;
	}
	$stringData .= "\r\n--------------------\r\n";
	fwrite($fh, $stringData);
	fclose($fh);
}


?>