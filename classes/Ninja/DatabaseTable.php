<?php
//This file creates a class 'DatabaseTable' that can be used to manipulate database tables
//This file creates functions used by other files (using include or require)
//Variable $query has been renamed $sql (compared to the book) to reduce confusion with the function 'query'

//namespace is like a folder and gives classes unique names, in case another developer creates an EntryPoint class
namespace Ninja;

class DatabaseTable {
	//These variables need to be provided where creating an instance of DatabaseTable
	//These variables can be used by all methods (functions) in the class, without being provided to the method separately
	private $pdo;
	private $table;
	private $primaryKey;
	private $className;
	private $constructorArgs;
	private $isMissingFrom;
	
	//__construct is used the first time a class is called and its parameters are set
	//PDOException has a '\' in front because we are in Ninja namespace
	//and PDOException is an in-built PHP class, in the global namespace
	//'\' tells it to start from global namespace
	//public function __construct(\PDO $pdo, string $table, string $primaryKey)
	public function __construct(\PDO $pdo, string $table, string $primaryKey, string $className = '\stdClass', array $constructorArgs = []) {
		$this->pdo = $pdo;
		$this->table = $table;
		$this->primaryKey = $primaryKey;
		$this->className = $className;
		$this->constructorArgs = $constructorArgs;
		$this->isMissingFrom = [];
	}

	// This method modifies the database table class so that SELECT queries run on
	// the table will only pick rows from this table where there is no counterpart
	// row on some other look-up table
	// Can be called multiple times to add multiple lookups, in which case the SELECT
	// query will be limited to rows that are in none of the missing from tables
	// If this method is never called then no missing from tables are set and SELECT
	// queries simply look at this database table in isolation
	//
	// Parameters:
	// $table      The other table in the database from which entries on this table 
	//             should be missing
	// $tableCol   The column on the other table that holds the same info as the current
	//             table. This is the column you join on to link the two tables
	// $sourceCol  The column in this table that holds the same info as the isMissingFrom
	//             table. This is the column you join on to link the two tables
	//             Optional - if ommitted then assume this table's primary key is the column
	//             to join on
	public function addIsMissingFrom($table, $tableCol, $sourceCol = null) {
		$isMissingFrom[] = [
			'table' => $table,
			'tableCol' => $tableCol,
			'sourceCol' => $sourceCol,
		];
	}

	// Helper funtion to build SELECT queries to account for any isMissingFrom tables
	// Builds a series of LEFT JOINS
	private function addMissingTablesJoin($sql) {
		if(!empty($this->isMissingFrom)) {
			foreach($this->isMissingFrom as $missingFrom) {
				$sourceCol = $missingFrom['sourceCol'] ?? $this->primaryKey;
				
				$sql .= ' LEFT JOIN `' . $missingFrom['table'] . '` ON';
				$sql .= ' `' . $missingFrom['table'] . '`.`' . $missingFrom['tableCol'] .'` =';
				$sql .= ' `' . $this->table . '`.`' . $sourceCol . '`';
			}
		}
		return $sql;
	}
	
	// Helper funtion to build SELECT queries to account for any isMissingFrom tables
	// Builds a series of WHERE constraints
	// $first parameter tracks if this is the first WHERE constraint. This is required
	// as the first one needs to be preceded by a 'WHERE' but all subsequent ones need
	// to be preceded by an 'AND'
	private function addMissingTablesWhere($first, $sql) {
		if(!empty($this->isMissingFrom)) {
			foreach($this->isMissingFrom as $missingFrom) {
				if ($first) {
					$sql .= ' WHERE';
					$first = false;
				} else {
					$sql .= ' AND';
				}
				$sql .= ' `' . $missingFrom['table'] . '`.`' . $missingFrom['tableCol'] .'` IS NULL';
			}
		}
		return $sql;
	}

	//This method creates an SQL query to be run on a database
	//The arguments are the sql query and parameters required by the sql query (which are set to an empty array [])
	//The prepare and execute parts ensure that special characters (e.g. ") don't corrupt the database
	private function query($sql, $parameters = []) {
		$query = $this->pdo->prepare($sql);
		$query->execute($parameters);
		return $query;
	}
		
	//This method returns the total number of records in any database table, matching the criteria set
	//The query it creates looks like:
	//SELECT COUNT(*) FROM `joke` WHERE `category` = Programmer jokes;
	public function total($field = null, $value = null) {
		$sql = 'SELECT COUNT(*) FROM `' . $this->table . '`';

		$sql = addMissingTablesJoin($sql);

		$parameters = [];
		$first = true;
		
		if (!empty($field)) {
			$sql .= ' WHERE `' . $field . '` = :value';
			$parameters = ['value' => $value];
			$first = false;
		}
		
		$sql = addMissingTablesWhere($first, $sql);
		
		$query = $this->query($sql, $parameters);
		$row = $query->fetch();
		return $row[0];
	}

	//This method selects a record from any database table
	//The query it creates looks like:
	//SELECT * FROM `joke` WHERE `primaryKey` =:3);
	public function findById($value) {
		$sql = 'SELECT * FROM `' . $this->table . '`';

		$sql = addMissingTablesJoin($sql);

		$sql .= ' WHERE `' . $this->primaryKey . '` = :value';
		$parameters = ['value' => $value];

		$sql = addMissingTablesWhere(false, $sql);

		$query = $this->query($sql, $parameters);
		return $query->fetchObject($this->className, $this->constructorArgs);
	}

	//This method finds all rows where any column is equal to a particular value
	//This can be used to check for duplicate email addresses
	//If $orderBy is set, the result will be ordered
	//If $limit is set, only the first $limit rows will be returned (e.g. $limit = 10, only the first 10 will be returned)
	//If $offset if set, e.g. 6 when $limit =10, then records 6 to 15 will be returned
	public function find($column, $value, $orderBy = null, $limit = null, $offset = null) {
		$sql = 'SELECT * FROM `' . $this->table . '`';

		$sql = addMissingTablesJoin($sql);

		$sql .= ' WHERE `' . $column . '` = :value';
		$parameters = ['value' => $value];

		$sql = addMissingTablesWhere(false, $sql);
		
		if ($orderBy !=null) {
			$sql .= ' ORDER BY ' . $orderBy;
		}
		
		if ($limit !=null) {
			$sql .= ' LIMIT ' . $limit;
		}
		
		if ($offset !=null) {
			$sql .= ' OFFSET ' . $offset;
		}
		
		$query = $this->query($sql, $parameters);
		
		//fetchAll returns an array (rather than an single value like fetch) 
		//so that more than one value can be returned
		//return $query->fetchAll();
		return $query->fetchAll(\PDO::FETCH_CLASS, $this->className, $this->constructorArgs);
	}

	//This method retrieves all records from any database table
	//The query it creates looks like:
	//SELECT * FROM `joke` ORDER BY date OFFSET 10;
	public function findAll($orderBy = null, $limit = null, $offset = null) {
		$sql = 'SELECT * FROM `' . $this->table . '`';

		$sql = addMissingTablesJoin($sql);
		$sql = addMissingTablesWhere(true, $sql);
		
		if ($orderBy !=null) {
			$sql .= ' ORDER BY ' . $orderBy;
		}
		
		if ($limit !=null) {
			$sql .= ' LIMIT ' . $limit;
		}
		
		if ($offset !=null) {
			$sql .= ' OFFSET ' . $offset;
		}
		
		$query = $this->query($sql);
		
		//return $result->fetchAll();
		return $query->fetchAll(\PDO::FETCH_CLASS, $this->className, $this->constructorArgs);
	}

	//This method converts DateTime objects to a string that MySQL understands
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

	//This method inserts a record in any database table
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
			
			//lastInsertId is a pdo method that reads the id of the last record inserted
			//this can store the primary key that is created by the database, which
			//can be used by save to set record the primary key on the created entity
			return $this->pdo->lastInsertId();
	} 

	//This method updates a record in any database table
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

	//This method saves changes to any database table
	//This may be inserting a new record or updating and existing record
	public function save($record) {
			
		//create an entity of the class to be updated/inserted
		//...allows specifying of an array in place of several arguments
		$entity = new $this->className(...$this->constructorArgs);
		
		try {
			//If it is a new record, the primary key will be empty, so set it to null and insert a new record
			//insert is defined in this DatabaseTable.php file
			if ($record[$this->primaryKey] == '') {
				$record[$this->primaryKey] = null;
			}
			
			//The output of the insert method is the id of the last record inserted,
			//which is the primary key of that record
			//This adds it to the entity
			$insertId = $this->insert($record);
			$entity->{$this->primaryKey} = $insertId;
		}
		//PDOException has a '\' in front because we are in Ninja namespace
		//and PDOException is an in-built PHP class, in the global namespace
		//'\' tells it to start from global namespace
		catch (\PDOException $error) {
			//Otherwise, if the primary key is not empty, update the existing record
			//update is defined in this DatabaseTable.php file
			$this->update($record);
		}
		
		//Write the data sent to the database to the class, converting the array to an object
		//Each time foreach iterates, $key is set to the column name and $value is set to the value being written to that column
		//if (!empty) prevents vlaues already set (such as primary keys) being overwritten with null
		foreach ($record as $key => $value) {
			if (!empty($value)) {
				$entity->$key = $value;	
			}			
		}
		
		//Output the entity so that we know the id of a new record, and 
		//so that the SELECT query is not required to fetch information back from the database that has just been inserted,
		//this improves performance as there are fewer demands on the database
		return $entity;
	}
	
	//This method deletes a record from any database table using its primary key
	//The query it creates looks like:
	//DELETE FROM `joke` WHERE `primaryKey` = :1;
	public function delete($id) {
		$sql = 'DELETE FROM `' . $this->table . '` WHERE `' . $this->primaryKey  . '` = :id';
		$parameters = ['id' => $id];
		$this->query($sql, $parameters);
	}
	
	//This method deletes records from any database table, where a particular column is equal to a particular value
	//The query it creas looks like:
	//DELETE FROM `joke` WHERE `authorId` = :1;
	public function deleteWhere($column, $value) {
		$sql = 'DELETE FROM `' . $this->table . '` WHERE `' . $column . '` = :value';
		$parameters = ['value' => $value];
		$this->query($sql, $parameters);
	}
}