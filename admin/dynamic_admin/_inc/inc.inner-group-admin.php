<div id="group-admin">
	<ul>
<?php
	
	if(isset($_SESSION['permission:creategroup'])){ 
		echo "<li><a href='index.php'><img src='../_img/back.png' alt='Return' title='Return'/><span>Return to Group List</span></a></li>";
		echo "<li class='border'><a href='create-group.php'><img src='../_img/add.png' alt='Add Group' title='Add Group'/><span>New Group</span></a></li>";
	}else{
		echo "<li class='border'><a href='index.php'><img src='../_img/back.png' alt='Return' title='Return'/><span>Return to Group List</span></a></li>";
	}
	
	if(isset($_SESSION['permission:editgroup'])){
		if($function != "edit-group"){
			echo "<li><a href=\"edit-group.php?id=" . $groupId . "\"><img src='../_img/users_edit.png' alt='Edit Group' title='Edit Group'/><span>Edit Group</span></a></li>";
		}else{
			echo "<li><p><img src='../_img/users_edit.png' alt='Edit Group' title='Edit Group'/><span>Edit Group</span></p></li>";
		}
	}
	if(isset($_SESSION['permission:definegroup'])){
		if($function != "define-group"){
			echo "<li><a href=\"define-group.php?id=" . $groupId . "\"><img src='../_img/users_process.png' alt='Define Group' title='Define Group'/><span>Define Group</span></a></li>";
		}else{
			echo "<li><p><img src='../_img/users_process.png' alt='Define Group' title='Define Group'/><span>Define Group</span></p></li>";
		}
	}
	if(isset($_SESSION['permission:addcourses'])){
		if($function != "add-course"){
			echo "<li><a href=\"add-course.php?id=" . $groupId . "\"><img src='../_img/application_add.png' alt='Add Courses' title='Add Courses'/><span>Add Courses</span></a></li>";
		}else{
			echo "<li><p><img src='../_img/application_add.png' alt='Add Courses' title='Add Courses'/><span>Add Courses</span></p></li>";
		}
	}
	if(isset($_SESSION['permission:viewusers'])){
		if($function != "view-users"){
			echo "<li><a href=\"view-users-group.php?id=" . $groupId . "\"><img src='../_img/users_search.png' alt='View Users' title='View Users' /><span>View Users</span></a></li>";
		}else{
			echo "<li><p><img src='../_img/users_search.png' alt='View Users' title='View Users' /><span>View Users</span></p></li>";
		}
	}
	if(isset($_SESSION['permission:assignmanager'])){
		if($function != "assign-manager"){
			echo "<li><a href=\"assign-manager-group.php?id=" . $groupId . "\"><img src='../_img/business_user_add.png' alt=\"Assign Manager\" title='Assign Manager'/><span>Assign Group Manager</span></a></li>";
		}else{
			echo "<li><p><img src='../_img/business_user_add.png' alt=\"Assign Manager\" title='Assign Manager'/><span>Assign Group Manager</span></p></li>";
		}
	}
	if(isset($_SESSION['permission:deletegroup'])){
		if($function != "delete-group"){
			echo "<li><a href=\"index.php?id=" . $groupId  .  "&action=delete\" onclick=\"return confirm('Are you sure?')\"><img src='../_img/delete.png' alt=\"Delete\" title='Delete Group'/><span>Delete Group</span></a></li>";
		}else{
			echo "<li><p><img src='../_img/delete.png' alt=\"Delete\" title='Delete Group'/><span>Delete Group</span></p></li>";
		}
	}
?>
</ul>
</div>