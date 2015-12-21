<?php

// MARK: interfaces

interface Controller {
	public function create();
	public function filter();
	public function save();
	// public function remove(); 
}

interface DatabaseManager {
	public function insertRow($table, $row); 
	public function getRows($table, $args);
	public function updateRow($table, $row); 
	public function deleteRowWithId($table, $id); 
}

interface CrudExtendable {
	public function toRow();
	public function fromRow($row);
}
