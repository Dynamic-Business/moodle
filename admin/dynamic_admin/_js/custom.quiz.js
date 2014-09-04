// Custom javascript JQUERY calls for all builder pages
$(document).ready(function(){
	$(function() {
		$("#datepickerto,#datepickerfrom ").datepicker({autoSize: true, maxDate: 'Now',width:100, dateFormat: 'dd/mm/yy' });
	});
});

//Enables/Disables other status and textfields when 'Not Started' is selected.
$(document).ready(function(){
	$('.not-started-cb').click(function () {
		if($(this).attr('checked')){
			//clear and disable values on left side
			$(this).attr('name', 'status[]');
			$(this).parent().prev().children("INPUT").removeAttr('name');
			$(this).parent().prev().children("INPUT").attr('checked', '');
			$(this).parent().prev().children("P").attr('class', 'disabled');
			$(this).parent().prev().children("INPUT").attr('disabled',true);
			$('#datepickerfrom').attr('disabled',true);
			$('#datepickerfrom').attr('value', 'N/A');
			$('#datepickerto').attr('disabled',true);
			$('#datepickerto').attr('value', 'N/A');
		}else{
			$(this).removeAttr('name');
			$(this).parent().prev().children("INPUT").attr('checked', true);
			$(this).parent().prev().children("INPUT").attr('name', 'status[]');
			$(this).parent().prev().children("P").attr('class', 'enabled');
			$(this).parent().prev().children("INPUT").removeAttr('disabled');
			$('#datepickerfrom').removeAttr('disabled');
			$('#datepickerfrom').attr('value', '01/05/2014');
			$('#datepickerto').removeAttr('disabled');
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
			$('#datepickerto').attr('value', now);
		}
			
	});
});