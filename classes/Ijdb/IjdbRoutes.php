<?php
//This file contains gspecific code for accessing the joke database
//DatabaseConnection.php sets up the connection to the database
//DatabaseTable.php contains functions to manipulate databases, including insert record, edit record and find record
//This file is called by including autoload.php in index.php
//\Ijdb\Controllers\Joke.php contains functions to manipulate the joke database
//RegisterController.php contains functions to administer users
//autoload.php loads class files when a class is used for the first time
//This code includes the \Ijdb\Controllers\Joke and RegisterController with their relevant classes as inputs

//namespace is like a folder and gives classes unique names, in case another developed creates an IjdbRoutes class
namespace Ijdb;

class IjdbRoutes
{	
	public function callAction($route)
	{
		include __DIR__ . '/../../includes/DatabaseConnection.php';

		//Create instances of DatabaseTables for the joke and author tables
		//The DatabaseTable class is in the Ninja namespace
		$jokesTable = new \Ninja\DatabaseTable($pdo, 'joke', 'id');
		$authorsTable = new \Ninja\DatabaseTable($pdo, 'author', 'id');
			
		//if $route is equal to and of the same type as 'joke/list', include \Ijdb\Controllers\Joke.php
		//Create instance of \Ijdb\Controllers\Joke with specific joke and author tables provided above
		//Set $page to \Ijdb\Controllers\Joke->list
		if ($route === 'joke/list') {
			$controller = new \Ijdb\Controllers\Joke($jokesTable, $authorsTable);
			$page = $controller->list();
		
		//otherwise, if route is equal to and of the same type as '' (i.e. empty), include \Ijdb\Controllers\Joke.php
		//Create instance of \Ijdb\Controllers\Joke with specific joke and author tables provided above
		//Set $page to \Ijdb\Controllers\Joke->home
		} elseif ($route === '') {
			$controller = new \Ijdb\Controllers\Joke($jokesTable, $authorsTable);
			$page = $controller->home();
		
		//otherwise, if route is equal to and of the same type as 'joke/edit', include \Ijdb\Controllers\Joke.php
		//Create instance of \Ijdb\Controllers\Joke with specific joke and author tables provided above
		//Set $page to \Ijdb\Controllers\Joke->edit
		} elseif ($route === 'joke/edit') {
			$controller = new \Ijdb\Controllers\Joke($jokesTable, $authorsTable);
			$page = $controller->edit();
		
		//otherwise, if route is equal to and of the same type as 'joke/delete', include \Ijdb\Controllers\Joke.php
		//Create instance of \Ijdb\Controllers\Joke with specific joke and author tables provided above
		//Set $page to \Ijdb\Controllers\Joke->delete
		} elseif ($route === 'joke/delete') {
			$controller = new \Ijdb\Controllers\Joke($jokesTable, $authorsTable);
			$page = $controller->delete();
			
		//otherwise, if route is equal to and of the same type as 'register', include Register.php
		//Create instance of \Ijdb\Controllers\Joke with specific joke and author tables provided above
		//Set $page to RegisterController->showform
		} elseif ($route === 'register') {
			$controller = new Register($authorsTable);
			$page = $controller->showform();
		}
		
		//This sets the output of this method
		return $page;
	}
}