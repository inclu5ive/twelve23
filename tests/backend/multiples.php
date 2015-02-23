<?php
// multiples.php : Joshua Lee
// twelve23 backend test : item 1

// instructions :
// 1) Write a script that prints the numbers from 1 to 100. For multiples of 3, print "three" instead of the number. 
// For multiples of 5, print "five". For numbers which are multiples of both 3 and 5 print "three-five."

// vars
$start = 1;
$end = 100;
$break_str = "<br />\n";

// run
for($num = $start; $num <= $end; $num++){
	$is_three = ($num % 3 == 0);
	$is_five = ($num % 5 == 0);
	$is_both = ($is_three && $is_five);
	
	$str = '';
	if($is_both){
		$str = 'three-five';
	} elseif($is_three) {
		$str = 'three';
	} elseif($is_five) {
		$str = 'five';
	}
	
	echo "{$num} : {$str}{$break_str}";
}

?>