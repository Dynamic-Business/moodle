<?php
	
	//new if statement so this is not called form DB create files. Already referenced in different way.
	if(!isset($dbscript)){
		require_once('/../../../config.php');
		require_once($CFG->libdir.'/adminlib.php');    // various admin-only functions
		//require_once('/../../lib/phpmailer/class.phpmailer.php'); //check this - may need another forward slash at start (local/Rackspace)
	}
	require_once('_inc/inc.innerlinks.php'); //Addded 7.7
	
	$CFG->dbpassword = $CFG->dbpass; //DB password variable name seems to have changed through Moodle 2 iterations.
	$selfPageRef = $_SERVER['PHP_SELF'];
	$startDateForPicker = "01/05/2012";
	$f2fTimeAdjust = 0; //weird date thing happening with Face to Face. Date it shows in interface is 2 hours in front, even though set to server time. This fixes it.
	$GroupManagerRoleID = array(99,100); //The id of the new group manager role
	$SiteManagerRoleID = 1; //The id of the new site manager role ('Manager' out of the box)
	$CFG->ErrorMessage = "Sorry but an unexpected error has occured. Please contact the Site Administrator.";

	/* configurable options*/

	/*Reporting Columns*/
	//available options are 'dbid','idnumber','institution','department','course name','lesson name','module name','attempt no',status','last modified','score'
	//will always return firstname and lastname regardless of options set
	$reportStandardColumns = array(/*"idnumber",*/
							"firstname",
							"lastname",
							"username",
							"course name",
							"lesson name",
							"module name",
							"attempt no",
							"status",
							"last modified",
							"score",
							//Additional Face to Face preferences - **NOT USED - now hardcoded
							"f2fsessionid",
							"f2fduration",
							"f2ftimestart",
							"f2ftimefinish",
							"f2fstatuscode");
							
	//These are the shortnames of the additional profile fields
	
	$reportAdditionalColumns = array(
	"dept",
	"contractedhours",
	"jobcode",
	"jobgrade",
	"storedetails",
	"region",
	"datestarted",
	/*ALL after here are hidden from reports*/
	"headteamcoach",
	"teamcoach",
	"homedesignconsultant",
	"stylist",
	"tailoring",
	"shoe",
	"firstaider",
	"appointed",
	"vandriver",
	"area",
	"homespecialist",
	"areavm",
	"banksman",
	"cia",
	"lingerie");
	
	//..and these are the corresponding ids of the shortnames							
	$reportAdditionalIds = array(
	5,
	6,
	3,
	4,
	2,
	21,
	1,
	/*ALL after here are hidden from reports*/
	12,
	11,
	13,
	14,
	15,
	16,
	17,
	27,
	18,
	20,
	22,
	23,
	24,
	25,
	26);
	
	//Email address to mail error reports to:
	$tableReportEmail = "pmcgovern@dynamicbusiness.co.uk";
	

	
?>