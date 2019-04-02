<?php

//This file creates functions used by other files (using include or require)
//Variable $query has been renamed $sql (compared to the book) to reduce confusion with the function 'query'

//This function creates an SQL query to be run on the database
//The arguments are the database connection, sql query and parameters required by the sql query (which are set to an empty array [])
//The prepare and execute parts ensure that special characters (e.g. ") don't corrupt the database
function query($pdo, $sql, $parameters = []) {
	$sql = $pdo->prepare($sql);
	$sql->execute($parameters);
	return $sql;
}

//This function converts DateTime objects to a string that MySQL understands
function processDates($fields) {
	foreach ($fields as $key => $value) {
		if ($value instanceof DateTime) {
			$fields[$key] = $value->format('Y-m-d');
		}
	}
	return $fields;
}

//This function retrieves all records from any database table
function findAll($pdo, $table) {
	$result = query($pdo, 'SELECT * FROM `' . $table . '`');
	
	return $result->fetchAll();
}

//This function deletes a record from any database table
function delete($pdo, $table, $primarykey, $id) {
	$parameters = [':id' => $id];
	
	query($pdo, 'DELETE FROM `' . $table . '` WHERE `' . $primarykey . '` = :id', $parameters);
}

//This function inserts a record in any database table
//The query it creates looks like:
//INSERT INTO `joke` (`joketext`, `jokedate`, `authorId`) VALUES (:joketext, :DateTime, :authorId);
function insert($pdo, $table, $fields) {
	$sql = 'INSERT INTO `' . $table . '` (';
	
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
		$fields = processDates($fields);
		
		query($pdo, $sql, $fields);
} 

//This function updates a record in any database table
//The query it creates looks like:
//UPDATE `joke` SET `joketext` = :joketext, `jokedate` = :DateTime, `authorId` = :authorId) WHERE `primaryKey` = :1;
function update($pdo, $table, $primaryKey, $fields) {
	
	$sql = 'UPDATE `' . $table . '` SET ';
	
	foreach ($fields as $key => $value) {
		$sql .= '`' . $key . '` = :' . $key . ',';
	}
	
	//Remove extraneous ',' from the query
	$sql = rtrim($sql, ',');
	
	$sql .= ' WHERE `' . $primaryKey . '` = :primaryKey';
	
	//Set the :primaryKey variable
	$fields['primaryKey'] = $fields['id'];
	
	//Change the date format to one MySQL can understand
	$fields = processDates($fields);
	
	query($pdo, $sql, $fields);	
}

//This function selects a record from any database table
function findById($pdo, $table, $primaryKey, $value) {
	
	$sql = 'SELECT * FROM `' . $table . '` WHERE `' . $primaryKey . '` = :value';
	
	$parameters = ['value' => $value];
	
	$sql = query($pdo, $sql, $parameters);
	
	return $sql->fetch();
}
	
//This function returns the total number of records in any database table
function total($pdo, $table) {
	
	$sql = query($pdo, 'SELECT COUNT(*) FROM `' . $table . '`');
	
	$row = $sql->fetch();
	
	return $row[0];
}

//This function saves changes to any database table
//This may be inserting a new record or updating and existing record
function save($pdo, $table, $primaryKey, $record) {
		try {
			//If it is a new record, the primary key will be empty, so set it to null and insert a new record
			//insert is defined in this DatabaseFunctions.php file
			if ($record[$primaryKey] == '') {
				$record[$primaryKey] = null;
			}
			
			insert($pdo, $table, $record);
		}
		catch (PDOException $error) {
			//Otherwise, if the primary key is not empty, update the existing record
			//update is defined in this DatabaseFunctions.php file
			update($pdo, $table, $primaryKey, $record);
		}
}