<?php	

//calc sq diff
function sqdiff($x, $mean) { 
	return pow($x - $mean,2); 
}

// calc sdv  
function sd($array) {
	// sqrt of sum of squares divided by n-1
	return sqrt(array_sum(array_map("sqdiff", $array, array_fill(0,count($array), (array_sum($array) / count($array)) ) ) ) / (count($array)-1) );
}