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

	//Create instances of DatabaseTables for the joke and author tables
	$jokesTable = new DatabaseTable($pdo, 'joke', 'id');
	$authorsTable = new DatabaseTable($pdo, 'author', 'id');
	
	
	//This code includes the JokeController and RegisterController with their relevant classes as inputs
	
	//if $route is not set, use 'joke/home'
	$route = $_GET['route'] ?? 'joke/home';
	
	//Use $route to ensure that the controllers only have classes as inputs that they are dependent upon
	//if $route is lowercase, go ahead and use it
	if ($route == strtolower($route)) {
		
		//if $route is equal to and of the same type as 'joke/list', include JokeController.php
		//Create instance of jokeController with specific joke and author tables provided above
		//Set $page to JokeController->list
		if ($route === 'joke/list') {
			include __DIR__ . '/../classes/controllers/JokeController.php';
			$controller = new JokeController($jokesTable, $authorsTable);
			$page = $controller->list();
		
		//otherwise, if route is equal to and of the same type as 'joke/home', include JokeController.php
		//Create instance of jokeController with specific joke and author tables provided above
		//Set $page to JokeController->home
		} elseif ($route === 'joke/home') {
			include __DIR__ . '/../classes/controllers/JokeController.php';
			$controller = new JokeController($jokesTable, $authorsTable);
			$page = $controller->home();
		
		//otherwise, if route is equal to and of the same type as 'joke/edit', include JokeController.php
		//Create instance of jokeController with specific joke and author tables provided above
		//Set $page to JokeController->edit
		} elseif ($route === 'joke/edit') {
			include __DIR__ . '/../classes/controllers/JokeController.php';
			$controller = new JokeController($jokesTable, $authorsTable);
			$page = $controller->edit();
		
		//otherwise, if route is equal to and of the same type as 'joke/delete', include JokeController.php
		//Create instance of jokeController with specific joke and author tables provided above
		//Set $page to JokeController->delete
		} elseif ($route === 'joke/delete') {
			include __DIR__ . '/../classes/controllers/JokeController.php';
			$controller = new JokeController($jokesTable, $authorsTable);
			$page = $controller->delete();
			
		//otherwise, if route is equal to and of the same type as 'register', include RegisterController.php
		//Create instance of jokeController with specific joke and author tables provided above
		//Set $page to RegisterController->showform
		} elseif ($route === 'register') {
			include __DIR__ . '/../classes/controllers/RegisterController.php';
			$controller = new JokeController($authorsTable);
			$page = $controller->showform();
		}
	//otherwise (if $route is not in lowercase), 
	//301 says this is a permanent rediret and redirects to the lowercase version
	// E.g. if someones visits index.php?action=ListJOKES, they will be redirected to index.php?action=listjokes
	//The permanent redirect is important for search engines not to include erroneous pages in their searches
	} else {
		http_response_code(301);
		header('location: index.php?route=' . strtolower($route));
	}
	
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

//This file contains the layout information and uses $title and $output defined above
include __DIR__ . '/../templates/layout.html.php';