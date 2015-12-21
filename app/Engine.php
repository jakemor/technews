<?php


require_once 'Response.php';
require_once 'Datastore.php';
require_once 'Assert.php';

class Engine {

	public $response;
	public $ds;
	private $assert;
	private $start_time;
	
	function __construct() {
		$this->response = new Response();
		$this->ds  = new Datastore();
		$this->assert = new Assert($this->ds);
	}

	public function send($should_print = true) {
		$delta = microtime(true) - $this->start_time;
		$this->response->add("time", $delta);
		
		if ($should_print) {
			$this->response->send();
		}

		$this->start_time = microtime(true);
		return $this->response->get(); 
	}

	public function startTimer() {
		$this->start_time = microtime(true);
	}

	public function assertKeys($args, $form) {
		foreach ($form as $key => $value) {

 			$param = $key;

 			if (!is_bool($value)) {
 				$param = $value;
 			}

 			// first check to see if the parameter was even given by the client

 			if (!array_key_exists($param, $args) ) {
 				$e = new Exception("Missing: {$param}", CLIENT_ERROR);
 				$this->response->addError($e);
 				continue;
 			}

 			// only continue if an assertion is required

 			if (is_bool($value)) {
	 			try {
	 				$method = "{$param}_exists";
	 				$result = $this->assert->$method($args[$param], $value); // returns true if exists in db, false if doesnt

				 	if ($result != $value) {
						if ($value) {
							throw new Exception(ucfirst("{$param} doesn't exist."), CLIENT_ERROR);
						} else {
							throw new Exception(ucfirst("{$param} exists."), CLIENT_ERROR);
						}
					}
	 			} catch (Exception $e) { // catch the thrown error so it can be displayed to the user
	 				$this->response->addError($e);
	 			}
 			}
		}

		if ($this->response->hasErrors()) {
			$this->send();
			exit();
		}
	}

}