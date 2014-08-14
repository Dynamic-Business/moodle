<?php

$time = 1281427514; //Tue, 10 Aug 2010 08:05:14 GMT
$daystart = $time - ($time%86400);
echo "start of day" . $daystart . "(" . date("d/m/y H:m:s",$daystart) . ")<br>" ;
//
$dayend = ($time - ($time%86400))+86399;
echo "end of day" . $dayend . "(" . date("d/m/y H:m:s",$dayend) . ")" ;


?>