<?php
//This file contains gspecific code for accessing the joke database
//DatabaseConnection.php sets up the connection to the database
//DatabaseTable.php contains functions to manipulate databases, including insert record, edit record and find record
//This file is called by including autoload.php in index.php
//JokeController.php contains functions to manipulate the joke database
//RegisterController.php contains functions to administer users
//autoload.php loads class files when a class is used for the first time
//This code includes the JokeController and RegisterController with their relevant classes as inputs

class IjdbRoutes
{	
	public function callAction($route)
	{
		include __DIR__ . '/../includes/DatabaseConnection.php';

		//Create instances of DatabaseTables for the joke and author tables
		$jokesTable = new DatabaseTable($pdo, 'joke', 'id');
		$authorsTable = new DatabaseTable($pdo, 'author', 'id');
			
		//if $route is equal to and of the same type as 'joke/list', include JokeController.php
		//Create instance of jokeController with specific joke and author tables provided above
		//Set $page to JokeController->list
		if ($route === 'joke/list') {
			include __DIR__ . '/../classes/controllers/JokeController.php';
			$controller = new JokeController($jokesTable, $authorsTable);
			$page = $controller->list();
		
		//otherwise, if route is equal to and of the same type as '' (i.e. empty), include JokeController.php
		//Create instance of jokeController with specific joke and author tables provided above
		//Set $page to JokeController->home
		} elseif ($route === '') {
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
		
		//This sets the output of this method
		return $page;
	}
}