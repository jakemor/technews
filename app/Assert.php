<?php

// MARK: constants

define("CLIENT_ERROR", 12151994);

class Assert {
	
	private $ds;

	function __construct($ds) {
		$this->ds = $ds;
	}

	// public function user_id_exists($id) {
	// 	$this->createUserObject();
	// 	$results = $this->user->filter("id", $id);
	// 	return sizeof($results) > 0;
	// }
}