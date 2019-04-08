<?php
//This file contains specific code for accessing the joke database
//DatabaseConnection.php sets up the connection to the database
//DatabaseTable.php contains functions to manipulate databases, including insert record, edit record and find record
//This file is called by including autoload.php in index.php
//\Ijdb\Controllers\Joke.php contains functions to manipulate the joke database
//RegisterController.php contains functions to administer users
//autoload.php loads class files when a class is used for the first time
//This code includes the \Ijdb\Controllers\Joke and RegisterController with their relevant classes as inputs

//namespace is like a folder and gives classes unique names, in case another developed creates an IjdbRoutes class
namespace Ijdb;

//Implements the type hinting defined in Routes.php
//This ensures the correct formats are used as inputs
class IjdbRoutes implements \Ninja\Routes
{	
	public function getRoutes()
	{
		include __DIR__ . '/../../includes/DatabaseConnection.php';

		//Create instances of DatabaseTables for the joke and author tables
		//The DatabaseTable class is in the Ninja namespace
		$jokesTable = new \Ninja\DatabaseTable($pdo, 'joke', 'id');
		$authorsTable = new \Ninja\DatabaseTable($pdo, 'author', 'id');

		//Create instance of jokeController with $jokesTable and $authorsTable as inputs
		$jokeController = new \Ijdb\Controllers\Joke($jokesTable, $authorsTable);
		
		//Create instance of authorController with $authorsTable as an inputs
		$authorController = new \Ijdb\Controllers\Register($authorsTable);
		
		//Create $routes array to enable URLs and request methods (_GET or _POST) to determine different actions
		$routes = [
			'joke/edit' => [
				'POST' => [
					'controller' => $jokeController, 
					'action' => 'saveEdit'],
				'GET' => [
					'controller' => $jokeController, 
					'action' => 'edit']],
			
			'joke/delete' => [
				'POST' => [
					'controller' => $jokeController, 
					'action' => 'delete']],
			
			'joke/list' => [
				'GET' => [
					'controller' => $jokeController, 
					'action' => 'list']],
						
			'' => [
				'GET' => [
					'controller' => $jokeController, 
					'action' => 'home']],
					
			'author/register' => [
				'GET' => [
					'controller' => $authorController,
					'action' => 'registrationForm'],
				'POST' => [
					'controller' => $authorController,
					'action' => 'registerUser']],
					
			'author/success' => [
				'GET' => [
					'controller' => $authorController,
					'action' => 'success']]
		];
		
		
		//Set the output of this function to be $routes
		//This array will contain the appropriate action, depending on the controller it is paired with
		return $routes;		
	}
}