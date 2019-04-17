<?php
//This file creates a class 'DatabaseTable' that can be used to manipulate database tables
//This file creates functions used by other files (using include or require)
//Variable $query has been renamed $sql (compared to the book) to reduce confusion with the function 'query'

//namespace is like a folder and gives classes unique names, in case another developed creates an EntryPoint class
namespace Ninja;

class DatabaseTable
{
	//These variables need to be provided where creating an instance of DatabaseTable
	//These variables can be used by all methods (functions) in the class, without be provided to the method separately
	private $pdo;
	private $table;
	private $primaryKey;
	private $className;
	private $constructorArgs;
	
	//__construct is used the first time a class is called and its parameters are set
	//PDOException has a '\' in front because we are in Ninja namespace
	//and PDOException is an in-built PHP class, in the global namespace
	//'\' tells it to start from global namespace
	//public function __construct(\PDO $pdo, string $table, string $primaryKey)
	public function __construct(\PDO $pdo, string $table, string $primaryKey, string $className = '\stdClass', array $constructorArgs = [])
	{
		$this->pdo = $pdo;
		$this->table = $table;
		$this->primaryKey = $primaryKey;
		$this->className = $className;
		$this->constructorArgs = $constructorArgs;
	}

	//This function creates an SQL query to be run on a database
	//The arguments are the database connection, sql query and parameters required by the sql query (which are set to an empty array [])
	//The prepare and execute parts ensure that special characters (e.g. ") don't corrupt the database
	private function query($sql, $parameters = []) {
		$sql = $this->pdo->prepare($sql);
		$sql->execute($parameters);
		return $sql;
	}
		
	//This function returns the total number of records in any database table
	//The query it creates looks like:
	//SELECT COUNT(*) FROM `joke`;
	public function total() {
		//Error $pdo and $table are not defined
		$sql = $this->query('SELECT COUNT(*) FROM `' . $this->table . '`');
		
		$row = $sql->fetch();
		
		return $row[0];
	}

	//This function selects a record from any database table
	//The query it creates looks like:
	//SELECT * FROM `joke` WHERE `primaryKey` =:3);
	public function findById($value) {
		
		$sql = 'SELECT * FROM `' . $this->table . '` WHERE `' . $this->primaryKey . '` = :value';
		
		$parameters = ['value' => $value];
		
		$sql = $this->query($sql, $parameters);
		
		return $sql->fetch();
		//return $sql->fetchObject($this->className, $this->constructorArgs);
	}

	//This function finds all rows where any column is equal to a particular value
	//This can be used to check for duplicate email addresses
	public function find($column, $value) {
		
		$sql = 'SELECT * FROM `' . $this->table . '` WHERE `' . $column . '` = :value';
		
		$parameters = ['value' => $value];
		
		$sql = $this->query($sql, $parameters);
		
		//fetchAll returns an array (rather than an single value like fetch) 
		//so that more than one value can be returned
		return $sql->fetchAll();
		//return $sql->fetchAll(\PDO::FETCH_CLASS, $this->className, $this->constructorArgs);
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
	//The query it creates looks like:
	//DELETE FROM `joke` WHERE `primaryKey` = :1;
	public function delete($id) {
		$parameters = [':id' => $id];

		$this->query('DELETE FROM `' . $this->table . '` WHERE `' . $this->primaryKey . '` = :id', $parameters);
	}

	//This function retrieves all records from any database table
	//The query it creates looks like:
	//SELECT * FROM `joke`;
	public function findAll() {
		$result = $this->query('SELECT * FROM `' . $this->table . '`');
		
		return $result->fetchAll();
		//return $result->fetchAll(\PDO::FETCH_CLASS, $this->className, $this->constructorArgs);
	}

	//This function converts DateTime objects to a string that MySQL understands
	//DateTime has a '\' in front because we are in Ninja namespace
	//and DateTime is an in-built PHP class, in the global namespace
	//'\' tells it to start from global namespace
	private function processDates($fields) {
		foreach ($fields as $key => $value) {
			if ($value instanceof \DateTime) {
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
				//insert is defined in this DatabaseTable.php file
				if ($record[$this->primaryKey] == '') {
					$record[$this->primaryKey] = null;
				}
				
				$this->insert($record);
			}
			//PDOException has a '\' in front because we are in Ninja namespace
			//and PDOException is an in-built PHP class, in the global namespace
			//'\' tells it to start from global namespace
			catch (\PDOException $error) {
				//Otherwise, if the primary key is not empty, update the existing record
				//update is defined in this DatabaseTable.php file
				$this->update($record);
			}
	}
}