<?php
//DatabaseConnection.php sets up the connection to the database
//DatabaseTable.php contains functions, including delete
//echo ("echo");
try {
	include __DIR__ . '/../includes/DatabaseConnection.php';
	include __DIR__ . '/../classes/DatabaseTable.php';
	
	$jokesTable = new DatabaseTable($pdo, 'joke', 'id');
	
	//delete is defined in DatabaseFunctions.php file
	//The inputs are the database connection (PDO) and the id that is retrieved when clicking the delete button (see jokes.html.php)
	$jokesTable->delete($_POST['id']);
	
	//Send the browser to jokes.php
	header('location: jokes.php');
	
}
	//If $pdo doesn't work, this provides an error message
	catch (PDOException $error) {
	$title = 'An error has occurred';
	
	$output = 'Unable to connect to the database server: ' . 
		$error->getMessage() . ' in ' .
		$error->getFile() . ':' . $error->getLine();
}

//This files contains the layout information
include __DIR__ . '/../templates/layout.html.php';