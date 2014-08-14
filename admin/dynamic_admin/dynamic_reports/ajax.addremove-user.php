<?php

/*
	Accepts an id of the profile field selected ('selected') and returns the relevant checkbox data.
	
	Example of jQuery Call can be found in 'hr-reportscorm-builder.php'
	
	//To update Profile Data depending on what is selected in Profile Field
	 $('.data-select').change(function () {
		var val = $(this).attr("selectedIndex");
		$('.ajax-cbg').load("ajax-profile-data.php" , {selected: val});
	 });
	 
	More info can be found: http://api.jquery.com/load/
	
*/

require_once('../config.php');
require_once('../_lib/form_lib.php');
require_once('../_lib/sql_lib.php');
	
//$userids = $_POST['userids'];
$userids = filter_var( $_POST['userids'], FILTER_SANITIZE_STRING); 

//$userids = "7,9175,8,99,98,55,3";

//$action = $_GET['action'];
$action = filter_var( $_GET['action'], FILTER_SANITIZE_STRING); 

if($userids != ""){
	$retVal = editUserList($userids,$action);
	if ($retVal == "max"){
		echo "<script type='text/javascript'>alert('You can only add a maximum of 5 users to this report');</script>";
	}
	if ($_SESSION["userids"] != ""){
		$sql = "SELECT id,firstname,lastname,idnumber FROM mdl_user WHERE id IN (" . $_SESSION["userids"]  . ") ORDER BY lastname " ;
		$rs = $DB->get_records_sql($sql);
		if(!empty($rs)){
			foreach($rs as $row) {
				echo "<input type='checkbox' name='userids[]' value=" . $row->id  .  " /><p>" . $row->firstname . " " . $row->lastname  . " (" . $row->idnumber . ") </p><br />" ;
			}
		}
			
	}else{
		echo "<p class='no-data'>-- No users added --</p>";	
	}

	//echo $sql;
}else{
	//No data sent
	
}

//Function to either add or remove users from the list.
function editUserList($userids,$action){
	//echo $userids;
	if($action == 'add'){
		
		if (isset($_SESSION["userids"]) && $_SESSION["userids"] != ""){
			$arrS = explode(",", $_SESSION["userids"]);
			$arrU = explode(",", $userids);
			//if the total is more than 5
			if ((count($arrS) + count($arrU)) <= 5){
				for ($i = 0;$i<count($arrU);$i++){
					$key = array_search($arrU[$i] , $arrS);
					if (!isset($key) || $key === false){
						//array_push($arrS,$arrU[$i]);
						$_SESSION["userids"] .= "," . $arrU[$i];
					}
				}
			}else{
				//what happens when number of users is over 5 
				return "max";
				
			}
			//$_SESSION["userids"] = join(',',$arrS);
		}else{
			$_SESSION["userids"] = $userids;
		}
		
		//echo $_SESSION["userids"];


		

	}else if($action == 'remove'){
		$arrU = explode(",", $userids);
		$arrS = explode(",", $_SESSION["userids"]);
		for ($i = 0;$i<count($arrU);$i++){
			$key = array_search($arrU[$i] , $arrS);
			if ($key !== false){
				unset($arrS[$key]);
			}
		}
		if (count($arrS) > 0){
			$_SESSION["userids"] = join(',',$arrS);
		}else{
			$_SESSION["userids"] = "";
		}
	}
}











?>