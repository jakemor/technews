<?php

// created by Jake Mor on Oct. 12, 2015

// MARK: datastore 
require_once 'Interfaces.php';

class Datastore implements DatabaseManager {
	// MARK: actually needed
	private $db;

	public function __construct() {
		//$db = new SQLite30('database.db');
		$sql_user = "";
		$sql_pass = "";

		$dsn = 'mysql:host=localhost;port=3306;dbname=thenews';

		$options = array(
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
		); 

		$this->db = new PDO($dsn, $sql_user, $sql_pass, $options);
	}

	public function insertRow($table, $row) {

		$colNames = array_keys($row);
		$colValues = array_values($row);

		$cols = "`" . implode("`, `", $colNames) . "`";

		foreach ($colValues as $key => $value) {
			if (is_null($value)) {
				$colValues[$key] = "NULL";
			} elseif (is_string($value)) {
				$val = $this->db->quote($value);
				$colValues[$key] = "{$val}";
			}
		}

		$vals = implode(", ", $colValues);

		$sql = "INSERT INTO `thenews`.`{$table}` ({$cols}) VALUES ({$vals})";

		try {
			$this->db->exec($sql);
			return null;
		} catch (PDOException $e) {
			//trigger_error($e);
			return $e;
		}

	}

	public function getRows($table, $args) {
		$sql = "SELECT * FROM `thenews`.`{$table}` WHERE ";

		// multiple options
		if (is_array($args[0])) {
			$join = isset($args[1]) ? (($args[1] == "OR") ? "OR" : "AND") : "AND";

			
			foreach ($args[0] as $key => $val) {
				$val = $this->db->quote($val);
				$sql .= "`{$key}` = {$val} {$join} ";
			}

			$sql = rtrim($sql, "{$join} ");

			$offset = 0;
			$limit = -1; 

			if (sizeof($args) == 4) {
				$limit = $args[2];
				$offset = $args[3];
			}

			if ($limit != -1) {
				$sql .= "LIMIT {$limit} ";
				$sql .= "OFFSET {$offset};"; 
			}
		// custom sql

		} elseif (is_string($args[0]) && sizeof($args) == 1) {

			$sql .= $args[0];

		// key value pair

		} elseif (is_string($args[0]) && sizeof($args) == 2) {
			$key = $args[0];
			$val = $args[1]; 
			$val = $this->db->quote($val);
			$sql .= "`{$key}` = {$val}";

		} elseif (is_string($args[0]) && sizeof($args) == 3) {
			$key = $args[0];
			$val = $args[1];
			$operator = $args[2];
			$val = $this->db->quote($val);
			$sql .= "`{$key}` {$operator} {$val}";
		
		} else {
			trigger_error("Ivalid use of Users->filter()");
			exit();
		}

		$sql .= " ORDER BY `created_at`";

		try {
			$result = $this->db->query($sql);
			return $result;
		} catch (PDOException $e) {
			trigger_error($e);
			return null;
		}
	}


	public function updateRow($table, $row, $unique = "id") {

		$sql = "UPDATE `thenews`.`{$table}` SET ";
		
		$id = $row[$unique]; 

		foreach ($row as $key => $val) {
			$val = $this->db->quote($val);
			$sql .= "`{$key}` = {$val}, "; 
		}

		$sql = rtrim($sql, ", ");

		$id = $this->db->quote($id);

		$sql .= " WHERE `{$unique}` = {$id}";

		try {
			$result = $this->db->query($sql);
			return null;
		} catch (PDOException $e) {
			//trigger_error($e);
			return $e;
		}

	}

	public function deleteRowWithId($table, $uid) {

		$sql = "DELETE FROM `thenews`.`{$table}` WHERE `id` = '{$uid}'"; // only id and hardcoded for safety

		try {
			$result = $this->db->query($sql);
			return $result;
		} catch (PDOException $e) {
			trigger_error($e);
			return $e;
		}

	}

	public function query($sql) {

		//$sql = $this->escapeSQL($sql);

		try {
			$result = $this->db->query($sql);
			return $result;
		} catch (PDOException $e) {
			trigger_error($e);
			return null;
		}
	}

	public function escapeSQL($sql) {
		$sql = str_replace("'", "''", $sql);
		$sql = str_replace("\\", "\\", $sql);
		return $sql;
	}
}


