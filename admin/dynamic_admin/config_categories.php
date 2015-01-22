<?php

	// These are the category titles which appear in the 'Category' dropdown menu in the report builders
	$categories_config = array( 
		array("1","Retail Academy Programmes"),
		array("2","Retail Academy - Salesfloor Activities"),
		array("3","Retail Academy - Home Activities"),
		array("4","Retail Academy - Delivery Activities"),
		array("5","Retail Academy - Administration Activities"),
		array("6","Retail Academy - Additional Learning"),
		array("7","Team Talks"),
		array("8","Management Academy")
	);

	// ================================================================================================
	// The number element in $categories_config correlate to the array element here. So if "3. Retail Academy - Home Activities" is selected
	// this will display $courses_config[3] below. id below is the actual course id.
	$courses_config = array();

	//1. Retail Academy Programmes
	$courses_config[1] = array(
		array("id"=>"81" , "fullname"=>"Salesfloor"),
		array("id"=>"8"  , "fullname"=>"Salesfloor and Home"),
		array("id"=>"26" , "fullname"=>"Delivery"),
		array("id"=>"27" , "fullname"=>"Administration")
	);

	//2. Retail Academy - Salesfloor Activities
	$courses_config[2] = array(
		array("id"=>"57" , "fullname"=>"Tills"),
		array("id"=>"58" , "fullname"=>"Using Your Initiative"),
		array("id"=>"7"  , "fullname"=>"Service"),
		array("id"=>"10" , "fullname"=>"Replenishment"),
		array("id"=>"12" , "fullname"=>"Product Knowledge"),
		array("id"=>"13" , "fullname"=>"Visual Merchandising")
	);

	//3. Retail Academy - Home Activities
	$courses_config[3] = array(
		array("id"=>"15" , "fullname"=>"Room Sets"),
		array("id"=>"16" , "fullname"=>"Shelved Stock")
	);

	//4. Retail Academy - Delivery Activities
	$courses_config[4] = array(
		array("id"=>"17" , "fullname"=>"Tubs/Sets"),
		array("id"=>"18" , "fullname"=>"Home Delivery"),
		array("id"=>"19" , "fullname"=>"3 Way Scan"),
		array("id"=>"20" , "fullname"=>"Orders"),
	);

	//5. Retail Academy - Administration Activities
	$courses_config[5] = array(
		array("id"=>"21" , "fullname"=>"Cash"),
		array("id"=>"22" , "fullname"=>"Stock"),
		array("id"=>"23" , "fullname"=>"Paperwork"),
		array("id"=>"24" , "fullname"=>"People")
	);

	//6. Retail Academy - Additional Learning
	$courses_config[6] = array(
		array("id"=>"28" , "fullname"=>"Product Specialist"),
		array("id"=>"29" , "fullname"=>"Tailoring Specialist"),
		array("id"=>"30" , "fullname"=>"Lingerie Specialist"),
		array("id"=>"31" , "fullname"=>"Style Advisor"),
		array("id"=>"32" , "fullname"=>"Dressing Room Sets"),
		array("id"=>"33" , "fullname"=>"Furniture Deliveries"),
		array("id"=>"34" , "fullname"=>"Delivery Table Leader"),
		array("id"=>"36" , "fullname"=>"Team Coach"),
		array("id"=>"77" , "fullname"=>"Merit Sales")
	);

	//7. Team Talks
	$courses_config[7] = array(
		array("id"=>"9" , "fullname"=>"Introducing Parcel ID...")
	);

	//8. Management Academy
	$courses_config[8] = array(
		array("id"=>"71" , "fullname"=>"Sales Co-ordinator"),
		array("id"=>"59" , "fullname"=>"Sales Manager"),
		array("id"=>"76" , "fullname"=>"Home Design Consultant"),
		array("id"=>"72" , "fullname"=>"Delivery Co-ordinator"),
		array("id"=>"64" , "fullname"=>"Delivery Manager"),
		array("id"=>"73" , "fullname"=>"Office Co-ordinator"),
		array("id"=>"66" , "fullname"=>"Office Manager"),
		array("id"=>"70" , "fullname"=>"Store Manager (with Deputy)"),
		array("id"=>"82" , "fullname"=>"Store Manager (with ASM)"),
		array("id"=>"69" , "fullname"=>"Operations Manager"),
		array("id"=>"68" , "fullname"=>"Commercial Manager"),
		array("id"=>"67" , "fullname"=>"Deputy Manager"),
		array("id"=>"74" , "fullname"=>"ASM")
	);	

?>