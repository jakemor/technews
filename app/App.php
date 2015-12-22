<?php


/*

for PHPMYADMIN
ssh -N -L 8888:127.0.0.1:80 -i key.pem bitnami@IP 

*/



// MARK: App
require_once __DIR__ . '/Engine.php';
require_once __DIR__ . '/User.php';
require_once __DIR__ . '/Source.php';

class App extends Engine {
	/* 
		
		MARK: public functions (endpoints)

	*/

	// import panda sources
	public function updateSources($get, $post) {
		$sources = file_get_contents("http://api.pnd.gs/v1/sources/");
		$sources = json_decode($sources, true);
		
		$my_sources = []; 

		foreach ($sources as $source) {

			// get source and store it in an array
			$my_source = [];
			$my_source["panda_id"] = $source["_id"];
			$my_source["key"] = $source["key"];
			$my_source["name"] = $source["name"];
			$my_source["description"] = $source["description"];
			$my_source["category"] = $source["category"];
			$my_source["type"] = $source["type"];
			$my_source["icon"] = $source["icon"];
			$my_source["color"] = $source["ios"]["color"];
			$my_source["popularity"] = $source["stats"]["popularity"]; 
			$my_source["popular_endpoint"] = NULL;
			$my_source["latest_endpoint"] = NULL;

			if ($source["popular"]) {
				$my_source["popular_endpoint"] = $source["endpoints"]["popular"];
			}

			if ($source["latest"]) {
				$my_source["latest_endpoint"] = $source["endpoints"]["latest"];
			}

			$display_settings = $source["settings"]["display"];

			// meta stuff
			$supported_meta = []; 

			if ($display_settings["views"]) {
				array_push($supported_meta, "views"); 
			}

			if ($display_settings["votes"]) {
				array_push($supported_meta, "votes"); 
			}

			if ($display_settings["comments"]) {
				array_push($supported_meta, "comments"); 
			}

			if ($display_settings["author"]) {
				array_push($supported_meta, "author"); 
			}

			$my_source["meta"] = implode(",", $supported_meta);

			$s = new Source($this->ds);
			$s = $s->withPandaId($my_source["panda_id"]);

			if (is_null($s)) {
				$s = new Source($this->ds);
			}

			$s->fromRow($my_source);
			$s->save();
			array_push($my_sources, $s);
		}

		function cmp($a, $b) {
			if ($a->popularity == $b->popularity) {
				return 0;
			}

			return ($a->popularity > $b->popularity) ? -1 : 1;
		}

		usort($my_sources, "cmp");

		$this->response->add("sources", $my_sources);
	}


	public function popularSources() {
		$s = new Source($this->ds); 
		$pop = $s->getPopularSources();
		$this->response->add("sources", $pop);
	}

	public function getPosts() {
		$this->response->shouldNotRespond();
		$user = new User($this->ds);
		$user = $user->withId("timestamp");
		$past = intval($user->username);
		$current = time();
		
		// only redownload sources if its been 20 min since last time
		if ($current - $past > 1200) {
			$user->username = time(); 
			$user->save(); 

			$s = new Source($this->ds); 
			$pop = $s->getPopularSources();
			
			$all_posts = [];

			foreach ($pop as $s) {
				$posts = $s->getPosts();
				$all_posts = array_merge($all_posts, $posts);
			}

			// TODO: only todays posts
			// for ($i=0; $i < sizeof($all_posts); $i++) { 
			// 	strtotime($all_posts[$i]["posted_at"])
			// }

			function cmp($a, $b) {
				if ($a->popularity == $b->popularity) {
					return 0;
				}

				return ($a->popularity > $b->popularity) ? -1 : 1;
			}

			usort($all_posts, "cmp");

			$file = fopen("posts.json","w+");
			fwrite($file,json_encode($all_posts));
			fclose($file);
		}

		$myfile = fopen("posts.json", "r") or die("Unable to open file!");
		echo fread($myfile,filesize("posts.json"));
		fclose($myfile);
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

