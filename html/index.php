<?php

//This file 

//This function includes files from the templates folder
//The file it includes depends of the $templateFileName given
//It also extracts (stores) variables that can be used in that file
function loadTemplate($templateFileName, $variables = [])
{
	extract($variables);
	
	
	//ob_start starts a buffer that gets filled by the include file and then output to the website at the end
	ob_start();

	include __DIR__ . '/../templates/' . $templateFileName;
	
	return ob_get_clean();
}

try {
//echo ('hi');
//DatabaseConnection.php sets up the connection to the database
//DatabaseTable.php contains functions, including save and findById
//JokeController.php 

	include __DIR__ . '/../includes/DatabaseConnection.php';

	include __DIR__ . '/../classes/DatabaseTable.php';

	include __DIR__ . '/../controllers/JokeController.php';

	$jokesTable = new DatabaseTable($pdo, 'joke', 'id');
	$authorsTable = new DatabaseTable($pdo, 'author', 'id');
	
	$jokeController = new JokeController($jokesTable, $authorsTable);
	
	$action = $_GET['action'] ?? 'home';
	
	$page = $jokeController->$action();
	
	$title = $page['title'];
	
	if (isset($page['variables'])) {
		$output = loadTemplate($page['template'], $page['variables']);
	} else {
		$output = loadTemplate($page['template']);
	}
	
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