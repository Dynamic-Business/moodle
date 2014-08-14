// Custom javascript JQUERY calls for all builder pages

$(document).ready(function(){ 
	 $('#styled-form').submit(function() {
		var $groupsfields = $(this).find('input[name="groups[]"]:checked');
		//var $coursesfields = $(this).find('input[name="courses[]"]:checked');
		var $statusfields = $(this).find('input[name="status[]"]:checked');
		if (!$groupsfields.length /*|| !$coursesfields.length*/  || !$statusfields.length ) {
			alert('At least one checkbox must be selected from each field');
			return false; // The form will *not* submit
		}
    });
});
