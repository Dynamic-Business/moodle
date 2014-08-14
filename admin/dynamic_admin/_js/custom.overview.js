// Custom javascript JQUERY calls for all builder pages

$(document).ready(function(){ 
	 $('#styled-form').submit(function() {
		var $profilefields = $(this).find('input[name="profiledata[]"]:checked');
		var $coursesfields = $(this).find('input[name="courses[]"]:checked');
		if (!$profilefields.length || !$coursesfields.length ) {
			alert('At least one checkbox must be selected from each field');
			return false; // The form will *not* submit
		}
	});
});
