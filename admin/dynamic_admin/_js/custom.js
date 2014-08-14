// Custom javascript JQUERY calls for all builder pages

//Makes Date Picker Work
$(document).ready(function() {
	$("#report-builder").validate({
	
		rules: {
			groups: {
				minlength: 2
			},
			datepickerfrom: {
				required: true
			}
		}
	
	
	});
});

//'Select All' and 'Clear' buttons on Report Builders
$(document).ready(function(){  
	 $(function() { 
		$('.check-options').click(function () {
			$(this).parent().prev().children("INPUT").attr('checked', 'checked');
		});
		$('.uncheck-options').click(function () {
			$(this).prev().parent().prev().children("INPUT").attr('checked', '');
		});
		//Used For Status checkboxes
		$('.check-options-status').click(function () {
			$(this).parent().prev().prev().children("INPUT").attr('checked', 'checked');
		});
		$('.uncheck-options-status').click(function () {
			$(this).prev().parent().prev().prev().children("INPUT").attr('checked', '');
		});
	 });
});


//AJAX calls to update status when a field is selected. For the HR report builders
$(document).ready(function(){
	//AJAX call - To update Profile Data depending on what is selected in Profile Field
	 $('.data-select').change(function () {
		//var val = $(this).attr("selectedIndex");
		var val = $(this).attr("value");
		//alert(val);
		$('.ajax-cbg').load("ajax.get-profile-data.php" , {selected: val});
	 });

});

$(document).ready(function(){
	//AJAX call - To update Attendance Data depending on what is selected in Session Status
	 $('.session-select').change(function () {
		//var val = $(this).attr("selectedIndex");
		var val = $(this).attr("value");
		//alert(val);
		$('.ajax-cba').load("ajax.get-attendance-data.php" , {selected: val});
	 });

});