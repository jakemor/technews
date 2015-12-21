<?php

// created by Jake Mor on Oct. 12, 2015

// MARK: Controller

require_once 'Token.php';
require_once 'Crud.php';

class User extends Crud implements JsonSerializable, CrudExtendable {

	private $username;
	private $password;
	private $first_name;
	private $middle_name;
	private $last_name;
	private $description;
	private $lat;
	private $lon;
	private $websites;
	private $dob;
	private $last_used;
	private $gender;
	private $profile_pic;
	private $phone;

	/* 
		MARK: functions
	*/

	public function setPhone($phone) {
		$this->phone = $phone;
	}

	public function getPhone() {
		return $this->phone;
	}

	public function generateToken() {
		$token = new Token($this->ds);
		$token->setUser($this);
		return $token;
	}

	public function setUsername($username) {
		$this->username = $username;
	}

	public function setPassword($password) {
		$salt = hash("crc32", uniqid() . $this->username);
		$hash = $salt . "." . hash("md5", $salt . $password);
		$this->password = $hash;
	}

	public function setUserInfo($info) {
		
		$fields = ["first_name", "middle_name", "last_name", "description", "websites", "gender"];

		foreach ($fields as $field) {
			if (array_key_exists($field, $info)) {
				if (is_string($info[$field])) {	
					$this->$field = $info[$field];
				}
			}
		}

	}

	public function verifyPassword($password) {
		$salthash = $this->password; 
		$a = explode(".", $salthash);
		$salt = $a[0];
		$stored_hash = $a[1];
		$given_hash = hash("md5", $salt . $password);

		return $stored_hash == $given_hash;
	}

	public function withId($id) {
		return $this->filter("id", $id)[0];
	}

	public function withPhone($phone) {
		return $this->filter("phone", $phone)[0];
	}

	public function withUsername($username) {
		return $this->filter("username", $username)[0];
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
			"username" => $this->username,
			"password" => $this->password,
			"first_name" => $this->first_name,
			"middle_name" => $this->middle_name,
			"last_name" => $this->last_name,
			"description" => $this->description,
			"lat" => $this->lat,
			"lon" => $this->lon,
			"phone" => $this->phone,
			"websites" => $this->websites,
			"dob" => $this->dob,
			"last_used" => $this->last_used,
			"gender" => $this->gender,
			"profile_pic" => $this->profile_pic,
			"created_at" => $this->created_at
		];
	}

	// MARK: determine which fields to return in a response
    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'profile_pic' => $this->profile_pic,
            'bio' => $this->description,
            'phone' => $this->phone
        ];
    }
}


































