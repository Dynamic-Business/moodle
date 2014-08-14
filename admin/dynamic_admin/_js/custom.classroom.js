// Custom javascript JQUERY calls for all builder pages

//disables date depending on what is selected
$(document).ready(function(){
	
	$('#datepickerfrom').attr('value', 'N/A');
	$('#datepickerfrom').attr('disabled',true);
	$('#datepickerto').attr('value', 'N/A');
	$('#datepickerto').attr('disabled',true);
				
				
	//AJAX call - To update Attendance Data depending on what is selected in Session Status
	 $('.session-select').change(function () {
		
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
			//Date Specified
			case "1":
				$("#datepickerto").datepicker("destroy"); //needed for new settings to take effect
				$("#datepickerfrom").datepicker("destroy"); //needed for new settings to take effect
				$("#datepickerto").datepicker({minDate: 'Now',maxDate: null, autoSize: true, width:100, dateFormat: 'dd/mm/yy'  });
				$("#datepickerfrom").datepicker({minDate: 'Now',maxDate: null, autoSize: true, width:100, dateFormat: 'dd/mm/yy'  });
				$('#datepickerto').removeAttr('disabled');
				$('#datepickerfrom').removeAttr('disabled');
				$('#datepickerfrom').attr('value', now);
				$('#datepickerto').attr('value', yearFromNow);
				
				
			break;
			//Waitlisted
			case "2":
				$('#datepickerfrom').attr('value', 'N/A');
				$('#datepickerfrom').attr('disabled',true);
				$('#datepickerto').attr('value', 'N/A');
				$('#datepickerto').attr('disabled',true);
			break;
			//Session Over
			case "3":
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
			//Enrolled on Course no bookings
			case "4":
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
