// Custom javascript JQUERY calls for all builder pages

//disables date depending on what is selected
$(document).ready(function(){
	//calls the ajax from the 'search' button
	$('.but-search').click(function () {
		var val = $('.search').val();

		$('.ajax-cbg').load("ajax.get-users-search.php" , {searchtext: val});
		
	});
	//calls ajax from the 'add' button
	$('.but-add').click(function () {
		 var useridsArr = [];
		 $('.ajax-cbg :checked').each(function() {
       		useridsArr.push($(this).val());
     	});
		var userids = useridsArr.join(",");
		//alert(userids);
		$('.search').val('');
		$('.ajax-cbg2').load("ajax.addremove-user.php?action=add" , {userids: userids});
	    $('.ajax-cbg').html("<p class='no-data'>-- no data available --</p>");
		
	});
	//calls ajax from the 'add' button
	$('.but-remove').click(function () {
		 var useridsArr = [];
		 $('.ajax-cbg2 :checked').each(function() {
       		useridsArr.push($(this).val());
     	});
		var userids = useridsArr.join(",");
		//alert(userids);
		$('.ajax-cbg2').load("ajax.addremove-user.php?action=remove" , {userids: userids});
	});

});

//Validator so user is not added 
$(document).ready(function(){ 
	 $('#styled-form').submit(function() {
		var $userids = $(this).find('input[name="userids[]"]');
		if (!$userids.length ) {
			alert('No users have been added to the report');
			return false; // The form will *not* submit
		}else{
			//solves bug when checkboxes are checked
			$('input[name=userids[]]').attr('checked', false);
		}
    });
});

