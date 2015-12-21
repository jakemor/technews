<?php


/*

for PHPMYADMIN
ssh -N -L 8888:127.0.0.1:80 -i key.pem bitnami@IP 

*/



// MARK: App
require_once __DIR__ . '/Engine.php';


class App extends Engine {
	/* 
		
		MARK: public functions (endpoints)

	*/

	public function test($get, $post) {

		$this->response->add("get", $get);
	}

	/* 
		
		MARK: private functions (not endpoints)

	*/



	/* 

		MARK: everytime() (required)

	*/

	public function everytime($get, $post) {


		return true;
	}

}

