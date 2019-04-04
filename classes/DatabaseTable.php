<?php

//This file creates functions used by other files (using include or require)
//Variable $query has been renamed $sql (compared to the book) to reduce confusion with the function 'query'

class DatabaseTable
{
	
	private $pdo;
	private $table;
	private $primaryKey;

	public function __construct(PDO $pdo, string $table, string $primaryKey)
	{
		$this->pdo = $pdo;
		$this->table = $table;
		$this->primaryKey = $primaryKey;
	}

	//This function creates an SQL query to be run on the database
	//The arguments are the database connection, sql query and parameters required by the sql query (which are set to an empty array [])
	//The prepare and execute parts ensure that special characters (e.g. ") don't corrupt the database
	private function query($sql, $parameters = []) {
		$sql = $this->pdo->prepare($sql);
		$sql->execute($parameters);
		return $sql;
	}
		
	//This function returns the total number of records in any database table
	public function total() {
		//Error $pdo and $table are not defined
		$sql = $this->query('SELECT COUNT(*) FROM `' . $this->table . '`');
		
		$row = $sql->fetch();
		
		return $row[0];
	}

	//This function selects a record from any database table
	public function findById($value) {
		
		$sql = 'SELECT * FROM `' . $this->table . '` WHERE `' . $this->primaryKey . '` = :value';
		
		$parameters = ['value' => $value];
		
		$sql = $this->query($sql, $parameters);
		
		return $sql->fetch();
	}

	//This function inserts a record in any database table
	//The query it creates looks like:
	//INSERT INTO `joke` (`joketext`, `jokedate`, `authorId`) VALUES (:joketext, :DateTime, :authorId);
	private function insert($fields) {
		$sql = 'INSERT INTO `' . $this->table . '` (';
		
		foreach ($fields as $key => $value) {
			$sql .= '`' . $key . '`,';
		}
		//Remove extraneous ',' from the query
		$sql = rtrim($sql, ',');
		
		$sql .= ') VALUES (';
		
		foreach ($fields as $key => $value ){
			$sql .= ':' . $key . ',';
		}
		
			//Remove extraneous ',' from the query
			$sql = rtrim($sql, ',');
			
			$sql .= ')';
			
			//Change the date format to one MySQL can understand
			$fields = $this->processDates($fields);
			
			$this->query($sql, $fields);
	} 

	//This function updates a record in any database table
	//The query it creates looks like:
	//UPDATE `joke` SET `joketext` = :joketext, `jokedate` = :DateTime, `authorId` = :authorId) WHERE `primaryKey` = :1;
	private function update($fields) {
		
		$sql = 'UPDATE `' . $this->table . '` SET ';
		
		foreach ($fields as $key => $value) {
			$sql .= '`' . $key . '` = :' . $key . ',';
		}
		
		//Remove extraneous ',' from the query
		$sql = rtrim($sql, ',');
		
		$sql .= ' WHERE `' . $this->primaryKey . '` = :primaryKey';
		
		//Set the :primaryKey variable
		$fields['primaryKey'] = $fields['id'];
		
		//Change the date format to one MySQL can understand
		$fields = $this->processDates($fields);
		
		$this->query($sql, $fields);	
	}

	//This function deletes a record from any database table
	public function delete($id) {
		$parameters = [':id' => $id];

		$this->query('DELETE FROM `' . $this->table . '` WHERE `' . $this->primaryKey . '` = :id', $parameters);
	}

	//This function retrieves all records from any database table
	public function findAll() {
		$result = $this->query('SELECT * FROM `' . $this->table . '`');
		
		return $result->fetchAll();
	}

	//This function converts DateTime objects to a string that MySQL understands
	private function processDates($fields) {
		foreach ($fields as $key => $value) {
			if ($value instanceof DateTime) {
				$fields[$key] = $value->format('Y-m-d');
			}
		}
		return $fields;
	}

	//This function saves changes to any database table
	//This may be inserting a new record or updating and existing record
	public function save($record) {
			try {
				//If it is a new record, the primary key will be empty, so set it to null and insert a new record
				//insert is defined in this DatabaseFunctions.php file
				if ($record[$this->primaryKey] == '') {
					$record[$this->primaryKey] = null;
				}
				
				$this->insert($record);
			}
			catch (PDOException $error) {
				//Otherwise, if the primary key is not empty, update the existing record
				//update is defined in this DatabaseFunctions.php file
				$this->update($record);
			}
	}
}