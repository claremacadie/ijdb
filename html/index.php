<?php

//This file is the main entry point to the website and uses EntryPoint.php and IjdbRoutes.php
//EntryPoint.php is generic code for accessing websites
//IjdbRoute.php is speccific code for accessing the joke database
//These files are called by including autoload.php
//autoload.php loads class files when a class is used for the first time
//Be aware, an extra file not in the book is required to get everything working
//This is a hidden file called .htaccess and ensures that unknown urls get sent to index.php


try {
//echo ('hi');
	//include calls the file
	// '/../' tells it to go up once from the directory it is in to find 'includes'
	include __DIR__ . '/../includes/autoload.php';
	
	//Set $route to whatever is written in the URL
	//By taking what is written up to the first ? and removing the initial /
	//E.g. /joke/edit?id=3 becomes joke/edit
	//jokes.html.php and layout.html.php set the URL to be something like: /joke/edit?id=<?=$joke['id']
	$route = ltrim(strtok($_SERVER['REQUEST_URI'], '?'), '/');
	
	//This sets up a new object called EntryPoint with $route as an input
	//The run method is defined in EntryPoint, which is in the namespace Ninja
	//(Similarly, IjdbRoutes is in the Ijdb namespace)
	//run uses layout.html.php to display stuff to the webpage (using $title and $output)
	$entryPoint = new \Ninja\EntryPoint($route, new \Ijdb\IjdbRoutes());
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