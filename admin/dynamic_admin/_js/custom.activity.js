// Custom javascript JQUERY calls for all builder pages

$(document).ready(function(){ 
	 $('#styled-form').submit(function() {
		var $groupsfields = $(this).find('input[name="groups[]"]:checked');
		if (!$groupsfields.length ) {
			alert('At least one group must be selected');
			return false; // The form will *not* submit
		}
		
	});
});

