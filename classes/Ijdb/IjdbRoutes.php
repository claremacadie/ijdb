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
	private $authorsTable;
	private $jokesTable;
	private $categoriesTable;
	private $jokeCategoriesTable;
	private $authentication;
	
	public function __construct()
	{
		include __DIR__ . '/../../includes/DatabaseConnection.php';

		//Create instances of DatabaseTables for the joke, author and joke category tables
		//The DatabaseTable class is in the Ninja namespace
		$this->jokesTable = new \Ninja\DatabaseTable($pdo, 'joke', 'id', '\Ijdb\Entity\Joke', [&$this->authorsTable, &$this->jokeCategoriesTable]);
		$this->authorsTable = new \Ninja\DatabaseTable($pdo, 'author', 'id', '\Ijdb\Entity\Author', [&$this->jokesTable]);
		$this->categoriesTable = new \Ninja\DatabaseTable($pdo, 'category', 'id', '\Ijdb\Entity\Category', [&$this->jokesTable, &$this->jokeCategoriesTable]);
		
		//Create instance of DatabaseTables for the joke_category table, 
		//which stores the many-many relationships between jokes and categories
		$this->jokeCategoriesTable = new \Ninja\DatabaseTable($pdo, 'joke_category', 'categoryId');	
			
		//Create an instance of the Authentication class (which is in the Ninja namespace)
		$this->authentication = new \Ninja\Authentication($this->authorsTable, 'email', 'password');		
	}

	//This creates a $routes array to enable URLs and request methods (_GET or _POST) to determine different actions
	//It uses type hinting to ensure it is array
	public function getRoutes(): array
	{
		//Create instance of jokeController with $jokesTable, $authorsTable, $categoriesTable and $authentication as inputs
		$jokeController = new \Ijdb\Controllers\Joke($this->jokesTable, $this->authorsTable, $this->categoriesTable, $this->jokeCategoriesTable, $this->authentication);
		
		//Create instance of authorController with $authorsTable as an input
		$authorController = new \Ijdb\Controllers\Register($this->authorsTable);
		
		//Create instance of loginController with $this->authentication as an input
		$loginController = new \Ijdb\Controllers\Login($this->authentication);
		
		//Create instance of categoryController with $this->categoriesTable as an input
		$categoryController = new \Ijdb\Controllers\Category($this->categoriesTable);
		
		//These routes appear in the address bar of the browser
		//They are used to determine which controller, 
		//and which method ('action') within that controller is called
		//They also use 'login' => true to ensure only specific actions are available to logged in users 
		$routes = [
			'joke/edit' => [
				'POST' => [
					'controller' => $jokeController, 
					'action' => 'saveEdit'],
				'GET' => [
					'controller' => $jokeController, 
					'action' => 'edit'],
				'login' => true],
			
			'joke/delete' => [
				'POST' => [
					'controller' => $jokeController, 
					'action' => 'delete'],
				'login' => true],
			
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
					'action' => 'success']],			
			
			'login' => [
				'GET' => [
					'controller' => $loginController,
					'action' => 'loginForm'],
				'POST' => [
					'controller' => $loginController,
					'action' => 'processLogin']],
			
			'login/success' => [
				'GET' => [
					'controller' => $loginController,
					'action' => 'success'],
				'login' => true],
			
			'login/error' => [
				'GET' => [
					'controller' => $loginController,
					'action' => 'error']],
					
			'logout' => [
				'GET' => [
					'controller' => $loginController,
					'action' => 'logout']],
					
			'category/edit' => [
				'POST' => [
					'controller' => $categoryController, 
					'action' => 'saveEdit'],
				'GET' => [
					'controller' => $categoryController, 
					'action' => 'edit'],
				'login' => true,
				'permissions' => \Ijdb\Entity\Author::EDIT_CATEGORIES],
			
			'category/delete' => [
				'POST' => [
					'controller' => $categoryController, 
					'action' => 'delete'],
				'login' => true,
				'permissions' => \Ijdb\Entity\Author::REMOVE_CATEGORIES],
				
			'category/list' => [
				'GET' => [
					'controller' => $categoryController, 
					'action' => 'list'],
				'login' => true,
				'permissions' => \Ijdb\Entity\Author::LIST_CATEGORIES],
					
			'author/permissions' => [
				'GET' => [
					'controller' => $authorController, 
					'action' => 'permissions'],
				'POST' => [
					'controller' => $authorController, 
					'action' => 'savePermissions'],
				'login' => true,
				'permissions' => \Ijdb\Entity\Author::EDIT_USER_ACCESS],
				
			'author/list' => [
				'GET' => [
					'controller' => $authorController,
					'action' => 'list'],
				'login' => true,
				'permissions' => \Ijdb\Entity\Author::EDIT_USER_ACCESS]
						
		];	
		
		
		//Set the output of this function to be $routes
		//This array will contain the appropriate action, depending on the controller it is paired with
		return $routes;		
	}

	//This function returns an authentication object defined by Authentication.php
	//It uses type hinting to ensure it is a Ninja/Authentication object
	public function getAuthentication(): \Ninja\Authentication
	{
		return $this->authentication;
	}
	
	//This function fetches the current logged-in user and checks if they have a specific permission
	//Check user is defined and their permissions match the relevant permission
	public function checkPermission($permission): bool {
		$user = $this->authentication->getUser();
		if ($user && $user->hasPermission($permission)) {
			return true;
		} else {
			return false;
		}
	}
	
}


