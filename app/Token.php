<?php

// created by Jake Mor on Oct. 14, 2015

// MARK: Controller

require_once 'Crud.php';

class Token extends Crud implements JsonSerializable, CrudExtendable {

	private $client_id;
	private $user_id;
	private $token;

	/* 
		MARK: functions
	*/

	public function setUser($user) {
		// remove all other tokens
		$tokens = $this->filter("user_id", $user->id);

		foreach ($tokens as $token) {
			$token->delete();
		}

		// add this token (uniqid() incorporates time so there can never be any duplicates)
		$this->user_id = $user->id;
		$this->token = uniqid() . hash("crc32", uniqid() . json_encode($user->toRow()));
		$this->save();
	}

	public function toString() {
		return $this->token;
	}

	public function getUserId() {
		return $this->user_id;
	}

	public function getTokenSecret() {
		return $this->token; 
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
			"client_id" => $this->client_id,
			"user_id" => $this->user_id,
			"token" => $this->token
		];
	}

	// MARK: determine which fields to return in a response
    public function jsonSerialize() {
        return $this->token;
    }
}


































