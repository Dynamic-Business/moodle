// Custom javascript JQUERY calls for all builder pages

$(document).ready(function(){ 
	 $('#styled-form').submit(function() {
		var $profilefields = $(this).find('input[name="profiledata[]"]:checked');
		//var $coursesfields = $(this).find('input[name="courses[]"]:checked');
		var $statusVal = $('.course-status-select  option:selected').val();
		if (!$profilefields.length /*|| !$coursesfields.length*/) {
			alert('At least one checkbox must be selected from each field');
			return false; // The form will *not* submit
		}else if ($statusVal == "-- please select") {
			alert('Please select a status');
			return false; // The form will *not* submit
	 	}
		
	});
});


//disables date depending on what is selected
$(document).ready(function(){
	
	$('#datepickerfrom').attr('value', 'N/A');
	$('#datepickerfrom').attr('disabled',true);
	$('#datepickerto').attr('value', 'N/A');
	$('#datepickerto').attr('disabled',true);
				
				
	//AJAX call - To update Attendance Data depending on what is selected in Session Status
	 $('.course-status-select').change(function () {
		
		var val = $(this).attr("value");
		var currentTime = new Date();
		var day = currentTime.getDate();
		if (day < 10){
			day = "0" + day;
		}
		var month = currentTime.getMonth() + 1;
		if (month < 10){
			month = "0" + month;
		}
		var year = currentTime.getFullYear();
		var now = day + "/" + month + "/" + year;
		var yearFromNow = day + "/" + month + "/" + (year+1);
		
		switch(val)
		{
			//Completed
			case "1":
				$("#datepickerto").datepicker("destroy"); //needed for new settings to take effect
				$("#datepickerfrom").datepicker("destroy"); //needed for new settings to take effect
				$("#datepickerto").datepicker({minDate: null,maxDate: 'Now', autoSize: true, width:100, dateFormat: 'dd/mm/yy'  });
				$("#datepickerfrom").datepicker({minDate: null,maxDate: 'Now', autoSize: true, width:100, dateFormat: 'dd/mm/yy'  });
				$('#datepickerto').removeAttr('disabled');
				$('#datepickerfrom').removeAttr('disabled');
				$('#datepickerfrom').attr('value', '01/05/2012');
				$('#datepickerto').attr('value', now);
				$("#datepickerto,#datepickerfrom ").datepicker({maxDate: 'Now',minDate: null});
			break;
			//Incomplete
			case "2":
				$("#datepickerto").datepicker("destroy"); //needed for new settings to take effect
				$("#datepickerfrom").datepicker("destroy"); //needed for new settings to take effect
				$("#datepickerto").datepicker({minDate: null,maxDate: 'Now', autoSize: true, width:100, dateFormat: 'dd/mm/yy'  });
				$("#datepickerfrom").datepicker({minDate: null,maxDate: 'Now', autoSize: true, width:100, dateFormat: 'dd/mm/yy'  });
				$('#datepickerto').removeAttr('disabled');
				$('#datepickerfrom').removeAttr('disabled');
				$('#datepickerfrom').attr('value', '01/05/2012');
				$('#datepickerto').attr('value', now);
				$("#datepickerto,#datepickerfrom ").datepicker({maxDate: 'Now',minDate: null});
			break;
			//Not started
			case "3":
				$('#datepickerfrom').attr('value', 'N/A');
				$('#datepickerfrom').attr('disabled',true);
				$('#datepickerto').attr('value', 'N/A');
				$('#datepickerto').attr('disabled',true);
			break;
			default:
			
			break;
		}
		
	 });

});
