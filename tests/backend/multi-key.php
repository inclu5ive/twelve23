<?php
// multi-key.php : Joshua Lee
// twelve23 backend test : item 2

// instructions :
// 2) Write a single function that determines whether or not an array key exists anywhere in a multi-dimensional array. 
// (Assume the array can have an arbitrary number of dimensions.)

// find_key : return true if key exists in multidimensional array, else return false
function find_key($arr, $key) {
	$result = 0;
	
	if( isset($arr[$key]) ){
		$result = 1;
	} else {
		foreach($arr as $sub_key => $sub_arr){
			if( is_array($sub_arr) ){
				$result += find_key($sub_arr, $key);
			}
		}
	}
	
	return ($result > 0);
}


// sample multi dim array
$sample_arr = array(
	'key21' => array(
		'key84' => array(),
		'key11' => array(
			'key93' => array(),
			'key42' => array()
		)
	),
	'key20' => array(
		'key18' => array(
			'key33' => array(
				'key5' => array()
			),
			'key36' => array(),
			'key51' => array()
		)
	),
	'key9' => array(
		'key88' => array(
			'key65' => array(
				'key40' => array()
			)
		),
		'key42' => array(
			'key74' => array()
		)
	)
);

/////////////////////////////////
// modify to search for given key
$search_key = 'key51'; 
/////////////////////////////////

// search
$result = find_key($sample_arr, $search_key); 

echo "key '{$search_key}' was " . ($result ? '' : 'not ') . "found";

?>