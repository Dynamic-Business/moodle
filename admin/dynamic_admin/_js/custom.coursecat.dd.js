//AJAX calls to update course when category is selected
$(document).ready(function(){
	//AJAX call - To update Profile Data depending on what is selected in Profile Field
	 $('.cat-select').change(function () {
		//var val = $(this).attr("selectedIndex");
		var val = $(this).attr("value");
		//alert(val);
		//$('.ajax-cbg2').load("ajax.get-coursecat.php" , {selected: val});
		$('.ajax-dd').load("ajax.get-coursecat-dd.php" , {selected: val});
	 });

	 //initla loading of Retail Academy
	 $('.ajax-dd').load("ajax.get-coursecat-dd.php" , {selected: 2});

});
