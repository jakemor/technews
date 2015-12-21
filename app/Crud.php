<?php

require_once "Interfaces.php";

class Crud implements Controller {

	// MARK: fields in db
	public $id;
	public $created_at;
	public $ds;

	// MARK: private vars
	private $table_name;

	// update("field", "value")
	function __construct($datastore = null, $row = null) {
		if (!is_null($datastore)) {
			$this->ds = $datastore;
		}

		if (!is_null($row)) {
			$this->fromRow($row);
		}

		$this->table_name = strtolower(get_class($this)); 
	}

	public function setTableName($name) {
		if (is_null($this->table_name)) {
			$this->table_name = $name; 
		} else {
			trigger_error("Crud instance can only have one table name.");
		}
	}

	public function create() { 

		if (is_null($this->ds)) {
			trigger_error("Crud instance doesn't have access to a Datastore.");
			exit();
		}
		
		if (!is_null($this->id)) {
			trigger_error("Crud Ids are assigned, no given.");
			exit();
		}

		$this->id = rand(100,999) . uniqid();
		
		$date = gmdate("Y-m-d H:i:s");
		$this->created_at = $date;

		$array = $this->toRow(); 

		$error = $this->ds->insertRow("{$this->table_name}", $array);

		return $error; 
	}


	public function filter() {

		if (is_null($this->ds)) {
			trigger_error("Crud instance doesn't have access to a Datastore.");
		}

		$args = func_get_args(); 

		if (sizeof($args) == 0) {
			trigger_error("Trying to filter without any params");
		}

		$result = $this->ds->getRows("{$this->table_name}", $args);

		$models = [];

		while ($modelInfo = $result->fetch()) {
			$model = new $this($this->ds, $modelInfo);
			array_push($models, $model);
		}

		return $models;
	}

	public function save() {
		if (is_null($this->id)) {
			return $this->create();
		} else {
			return $this->ds->updateRow("{$this->table_name}", $this->toRow());
		}
	}

	public function delete() {
		$this->ds->deleteRowWithId("{$this->table_name}", $this->id);
	}

	// TODO: add remove()

}