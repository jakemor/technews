<?php

// versatile response object that deals with adding users, videos, error messages and more to the response message.

// MARK: response object

class Response {

	private $response;
	private $shouldRespond;

	function __construct() {
		$this->response = array();
		$this->shouldRespond = true;
	}

	public function shouldNotRespond() {
		$this->shouldRespond = false;
	}

	public function get() {
		return $this->response;
	}

	public function addError($e) {
		if ($e->getCode() == CLIENT_ERROR) {
			http_response_code(500);
			$this->add("errors", [$e->getMessage()]);
		}
	}

	public function hasErrors() {
		if (array_key_exists("errors", $this->response)) {
			return true;
		}

		return false;
	}


	public function clear($label) {
		if (array_key_exists($label, $this->response)) {
			unset($this->response[$label]);
		}
	}

	// basic function to add to response

	public function add($label, $array) {
		if (!array_key_exists($label, $this->response)) {
			$this->response[$label] = [];
		}

		if (!is_array($array)) {
			$array = [$array];
		}

		$this->response[$label] = array_merge($this->response[$label], $array);
	}

	public function send() {
		if ($this->shouldRespond) {
			$this->cleanUp();
			echo json_encode($this->response);
		}
		exit();
	}

	private function cleanUp() {
		foreach ($this->response as $key => $value) {
			if ($key == "errors") {
				continue;
			}
			if ((sizeof($this->response[$key]) == 1) && !empty($this->response[$key][0]) ) {
				$this->response[$key] = $this->response[$key][0];
			}
		}
	}



}
