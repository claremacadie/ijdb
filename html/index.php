<?php

//This file is the main entry point to the website and uses EntryPoint.php and IjdbRoutes.php
//EntryPoint.php is generic code for accessing websites
//IjdbRoute.php is speccific code for accessing the joke database
//These files are called by including autoload.php
//autoload.php loads class files when a class is used for the first time
//Be aware, an extra file not in the book is required to get everything working
//This is a hidden file called .htaccess and ensures that unknown urls get sent to index.php

//A word about namespaces: these are used similar to file directories, 
//they organise classes into generic and specific types
//When namespaces are created they are referred to like this: namespace Ninja
//When they are subsquently called either as a type hint or when creating new objects
//they are referred to like this: \Ninja\

try {
	//include calls the file
	// '/../' tells it to go up once from the directory it is in to find 'includes'
	include __DIR__ . '/../includes/autoload.php';
	
	//Set $route to whatever is written in the URL
	//E.g. /joke/edit?id=3 becomes joke/edit
	$route = ltrim(strtok($_SERVER['REQUEST_URI'], '?'), '/');
		
	//This sets up a new object called EntryPoint 
	//with inputs $route, $method (_GET or _POST) and $routes (defined by IjdbRoutes)
	//The run method is defined in EntryPoint, which is in the namespace Ninja
	//(Similarly, IjdbRoutes is in the Ijdb namespace)
	//run uses layout.html.php to display stuff to the webpage (using $title and $output)
	$entryPoint = new \Ninja\EntryPoint($route, $_SERVER['REQUEST_METHOD'], new \Ijdb\IjdbRoutes());
	$entryPoint->run();
	
//If $pdo (Database connection) doesn't work, this provides an error message
} catch (PDOException $error) {
	$title = 'An error has occurred';
	
	$output = 'Unable to connect to the database server: ' . 
		$error->getMessage() . ' in ' .
		$error->getFile() . ':' . $error->getLine();

	//This file contains the layout information and uses $title and $output defined above
	include __DIR__ . '/../templates/layout.html.php';
}