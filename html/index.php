<?php

//This file is the main entry point to the website
//This files changes what is displayed on the webpage based on what information has been posted (_POST or _GET) 

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
//DatabaseTable.php contains functions to manipulate databases, including insert record, edit record and find record
//JokeController.php contains functions to manipulate the joke database

	include __DIR__ . '/../includes/DatabaseConnection.php';

	include __DIR__ . '/../classes/DatabaseTable.php';

	include __DIR__ . '/../controllers/JokeController.php';

	//Create instances of DatabaseTables for the joke and author tables
	$jokesTable = new DatabaseTable($pdo, 'joke', 'id');
	$authorsTable = new DatabaseTable($pdo, 'author', 'id');
	
	//Create instance of jokeController with specific joke and author tables provided above
	$jokeController = new JokeController($jokesTable, $authorsTable);
	
	//Define $action to be what is in _GET['action']
	//If there is no _GET['action'] set, then set it to home
	$action = $_GET['action'] ?? 'home';
	
	//This uses $action detemined above to select the associated methods in jokeController
	//E.g., if $action = 'list', use the list method (function) as defined in jokeController
	$page = $jokeController->$action();
	
	//Define $title as whatever is output by the method used above
	$title = $page['title'];
	
	//If the action method (home, list, edit, delete) defined variables, 
	//pass them to the loadTemplate function (defined above) along with $page['template']
	//$output is used in layout.html.php and sets what is put in the main body of the web page
	if (isset($page['variables'])) {
		$output = loadTemplate($page['template'], $page['variables']);
	//Otherwise, just pass $page['template'] to loadTemplate	
	} else {
		$output = loadTemplate($page['template']);
	}
	
} 	

//If $pdo (Database connection) doesn't work, this provides an error message
	catch (PDOException $error) {
	$title = 'An error has occurred';
	
	$output = 'Unable to connect to the database server: ' . 
		$error->getMessage() . ' in ' .
		$error->getFile() . ':' . $error->getLine();
}

//This file contains the layout information
//This uses $title and $output defined above
	include __DIR__ . '/../templates/layout.html.php';