<?php
//DatabaseConnection.php sets up the connection to the database
//DatabaseTable.php contains functions, including findAll, findById and total
//echo ("echo");

try {
	
	include __DIR__ . '/../includes/DatabaseConnection.php';
	
	include __DIR__ . '/../classes/DatabaseTable.php';
	
	$jokesTable = new DatabaseTable($pdo, 'joke', 'id');
	$authorsTable = new DatabaseTable($pdo, 'author', 'id');

		
	//Use the FindAll function (defined in DatabaseFunctions.php) to return a list of all the jokes in the database
	$result = $jokesTable->findAll($pdo, 'joke');
	
	//Create an array ($jokes) for jokes.html.php to iterate to produce the list of jokes
	$jokes = [];
	foreach ($result as $joke) {
		$author = $authorsTable->findById($joke['authorid']);
		
		$jokes[] = [
			'id' => $joke['id'],
			'joketext' => $joke['joketext'],
			'jokedate' => $joke['jokedate'],
			'name' => $author['name'],
			'email' => $author['email']
		];
	}
	
	//Set variable 'title' for use in the include file
	$title = 'Joke list';
	
	//Use total (defined in DatabaseFunctions.php) to return the total number of jokes
	$totalJokes = $jokesTable->total();
	
	//ob_start starts a buffer that gets filled by the include file and then output to the website at the end
	ob_start();
	include __DIR__ . '/../templates/jokes.html.php';
	$output = ob_get_clean();
	
//If $pdo doesn't work, this provides an error message
} catch (PDOException $error) {
	$title = 'An error has occurred';
	
	$output = 'Unable to connect to the database server: ' . 
		$error->getMessage() . ' in ' .
		$error->getFile() . ':' . $error->getLine();
}
//This files contains the layout information
include __DIR__ . '/../templates/layout.html.php';