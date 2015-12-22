<?php

// created by Jake Mor on Oct. 14, 2015

// MARK: Controller

require_once __DIR__ . '/Crud.php';
require_once __DIR__ . '/Post.php';

class Source extends Crud implements JsonSerializable, CrudExtendable {

	public $panda_id;
	public $key;
	public $name;
	public $description;
	public $category;
	public $type;
	public $icon;
	public $color;
	public $popularity;
	public $popular_endpoint;
	public $latest_endpoint;
	public $meta;
	public $updated_at; 

	/* 
		MARK: functions
	*/
	public function withPandaId($id) {

		if (sizeof($this->filter("panda_id", $id)) > 0) {
			return $this->filter("panda_id", $id)[0];
		} else {
			return null; 
		}
	}

	public function getPopularSources() {
		return $this->filter("`type` = 'news' AND `popular_endpoint` != '' AND `popularity` >= 1500 AND `meta` LIKE '%votes%'");
	}

	public function getPosts() {
		$url = $this->popular_endpoint;
		$panda_posts = file_get_contents($url); 
		$panda_posts = json_decode($panda_posts, true);

		$posts = []; 
		
		$vals =[]; 
		$sum = 0; 
		foreach ($panda_posts as $pp) {
			$p = new Post($this->ds);
			$p = $p->fromPandaPost($this, $pp);
			$p->source_name = $this->name;
			$p->source_icon = $this->icon;
			$sum += $p->likes;
			array_push($vals, $p->likes);
			array_push($posts, $p);
		}

		require_once "Stats.php";

		$sdv = sd($vals);
		$mean = $sum/sizeof($panda_posts);

		$min = 0; 

		foreach ($posts as $p) {
			$p->popularity = ($p->likes-$mean)/$sdv;
			$min = min($p->popularity, $min); 
		}

		$min = abs($min) + 1; 

		foreach ($posts as $p) {
			$p->popularity = ((($p->likes-$mean)/$sdv) + $min)*$this->popularity;
		}

		return $posts; 
	}

	/* 
		 MARK: required implementations
	*/

	// MARK: fromRow
	public function fromRow($row) {
		foreach ($row as $prop => $value) {
			if (property_exists($this, $prop)) {
				$this->$prop = $value; 
			}
		}
	}

	// MARK: map db columns to class properties  
	public function toRow() {
		return [
			"id" => $this->id,
			"created_at" => $this->created_at,
			"panda_id" => $this->panda_id,
			"key" => $this->key,
			"name" => $this->name,
			"description" => $this->description,
			"category" => $this->category,
			"type" => $this->type,
			"icon" => $this->icon,
			"color" => $this->color,
			"popularity" => $this->popularity,
			"popular_endpoint" => $this->popular_endpoint,
			"latest_endpoint" => $this->latest_endpoint,
			"meta" => $this->meta
		];
	}

	// MARK: determine which fields to return in a response
    public function jsonSerialize() {
        return [
			"id" => $this->id,
			"created_at" => $this->created_at,
			"updated_at" => $this->updated_at,
			"panda_id" => $this->panda_id,
			"key" => $this->key,
			"name" => $this->name,
			"description" => $this->description,
			"category" => $this->category,
			"type" => $this->type,
			"icon" => $this->icon,
			"color" => $this->color,
			"popularity" => $this->popularity,
			"popular_endpoint" => $this->popular_endpoint,
			"latest_endpoint" => $this->latest_endpoint,
			"meta" => $this->meta
        ];
    }
}

