<?php

// created by Jake Mor on Oct. 14, 2015

// MARK: Controller

require_once __DIR__ . '/Crud.php';

class Post extends Crud implements JsonSerializable, CrudExtendable {

	public $title;
	public $likes;
	public $comments;
	public $iframe_support;
	public $target_url;
	public $panda_url;
	public $description;
	public $source_id;
	public $panda_id;
	public $posted_at;
	public $img_big;
	public $img_normal;
	public $img_small;
	public $updated_at;
	public $popularity;
	public $source_name;
	public $source_icon;
	public $rank;

	/* 
		MARK: functions
	*/

	public function fromPandaPost($source, $pp, $rank) {
		$panda_id = $pp["uniqueid"];

		$p = $this;
		$r = $this->filter("panda_id", $panda_id); 

		if (sizeof($r) > 0) {
			$p = $r[0];
		}

		$p->panda_id = $panda_id;
		$p->title = $pp["title"];
		
		if (array_key_exists("likesCount", $pp["source"])) {
			$p->likes = $pp["source"]["likesCount"];
		}

		$p->rank = $rank;

		if (array_key_exists("commentsCount", $pp["source"])) {
			$p->comments = $pp["source"]["commentsCount"];
		}
		
		$iframe = $pp["flags"]["iframe"]["supported"]; 

		if ($iframe == 1) {
			$p->iframe_support = 1;
		} else {
			$p->iframe_support = 0;
		}

		$target = "";

		if (array_key_exists("target", $pp["url"])) {
			$target = $pp["url"]["target"];
		} else {
			$target = $pp["source"]["targetUrl"];
		}

		$p->target_url = $this->ds->escapeSQL($target);
		$p->panda_url = $this->ds->escapeSQL($pp["source"]["sourceUrl"]);
		$p->description = $pp["description"];
		$p->source_id = $source->id;
		$p->posted_at = date("Y-m-d H:i:s", strtotime($pp["source"]["createdAt"]));
		
		# usage
		$options['force']     = 'false';      # [false,always,timestamp] Default: false
		$options['fullpage']  = 'false';      # [true,false] Default: false
		$options['thumbnail_max_width'] = 'false';      # scaled image width in pixels; Default no-scaling.
		$options['viewport']  = "700x432";  # Max 5000x5000; Default 1280x1024

		$src = $this->url2png_v6($target, $options);

		$p->img_big = $this->ds->escapeSQL($pp["image"]["big"]);
		$p->img_normal = $this->ds->escapeSQL($src);
		$p->img_small = $this->ds->escapeSQL($pp["image"]["small"]);

		$e = $p->save(); 



		return $p;
	}

	private function url2png_v6($url, $args) {

		$URL2PNG_APIKEY = "PEEC6B0BD2FC0A4";
		$URL2PNG_SECRET = "S_A73053009DA49";

		# urlencode request target
		$options['url'] = urlencode($url);

		$options += $args;

		# create the query string based on the options
		foreach($options as $key => $value) { $_parts[] = "$key=$value"; }

		# create a token from the ENTIRE query string
		$query_string = implode("&", $_parts);
		$TOKEN = md5($query_string . $URL2PNG_SECRET);

		return "https://api.url2png.com/v6/$URL2PNG_APIKEY/$TOKEN/png/?$query_string";

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
			"posted_at" => $this->posted_at,
			"id" => $this->id,
			"created_at" => $this->created_at,
			"title" => $this->title,
			"likes" => $this->likes,
			"comments" => $this->comments,
			"iframe_support" => $this->iframe_support,
			"target_url" => $this->target_url,
			"panda_url" => $this->panda_url,
			"description" => $this->description,
			"source_id" => $this->source_id,
			"panda_id" => $this->panda_id,
			"img_big" => $this->img_big,
			"img_normal" => $this->img_normal,
			"img_small" => $this->img_small,
			"rank" => $this->rank,
		];
	}

	// MARK: determine which fields to return in a response
    public function jsonSerialize() {
        return [
			"updated_at" => $this->updated_at,
			"id" => $this->id,
			"created_at" => $this->created_at,
			"posted_at" => $this->posted_at,
			"title" => $this->title,
			"likes" => $this->likes,
			"comments" => $this->comments,
			"iframe_support" => $this->iframe_support,
			"target_url" => $this->target_url,
			"source_url" => $this->panda_url,
			"description" => $this->description,
			"source_id" => $this->source_id,
			"panda_id" => $this->panda_id,
			"img_big" => $this->img_big,
			"img_normal" => $this->img_normal,
			"img_small" => $this->img_small,
			"popularity" => $this->popularity,
			"source_name" => $this->source_name,
			"source_icon" => $this->source_icon,
			"rank" => $this->rank,
        ];
    }
}

