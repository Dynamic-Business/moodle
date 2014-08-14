// Custom javascript JQUERY calls for all builder pages

$(document).ready(function(){ 
	 $('#styled-form').submit(function() {
		var $groupsfields = $(this).find('input[name="groups[]"]:checked');
		//var $coursesfields = $(this).find('input[name="courses[]"]:checked');
		if (!$groupsfields.length /*|| !$coursesfields.length*/) {
			alert('At least one Group must be selected');
			return false; // The form will *not* submit
		}
		
	});
});

