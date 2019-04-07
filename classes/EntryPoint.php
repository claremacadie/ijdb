<?php
//This file changes what is displayed on the webpage based on $route as defined in index.php
//It defines $title and $outut which are used by layout.html.php to display stuff to the webpage

class EntryPoint
{
	private $route;
	
	public function __construct($route)
	{
		$this->route = $route;
		$this->checkUrl();
	}
	
	//Check if $route is not in lowercase, 
	//301 says this is a permanent rediret and redirects to the lowercase version
	// E.g. if someones visits index.php?action=ListJOKES, they will be redirected to index.php?action=listjokes
	//The permanent redirect is important for search engines not to include erroneous pages in their searches
	//Once redirected to index.php, it eventually comes back into this file, 
	//but passes the lowercase test and so does the callAction method below
	
	private function checkUrl()
	{
		if ($this->route !== strtolower($this->route)) {
			http_response_code(301);
			header('location: ' . strtolower($this->route));
		}
	}
	
	//This function includes files from the templates folder
	//The file it includes depends of the $templateFileName given
	//It also extracts (stores) variables that can be used in that file
	private function loadTemplate($templateFileName, $variables = [])
	{
		extract($variables);
		
		
		//ob_start starts a buffer that gets filled by the include file and then output to the website at the end
		ob_start();

		include __DIR__ . '/../templates/' . $templateFileName;
		
		return ob_get_clean();
	}

	//DatabaseConnection.php sets up the connection to the database
	//DatabaseTable.php contains functions to manipulate databases, including insert record, edit record and find record
	//JokeController.php contains functions to manipulate the joke database
	//RegisterController.php contains functions to administer users
	//This code includes the JokeController and RegisterController with their relevant classes as inputs
		
	private function callAction()
	{
		include __DIR__ . '/../includes/DatabaseConnection.php';
		include __DIR__ . '/../classes/DatabaseTable.php';

		//Create instances of DatabaseTables for the joke and author tables
		$jokesTable = new DatabaseTable($pdo, 'joke', 'id');
		$authorsTable = new DatabaseTable($pdo, 'author', 'id');
			
		//if $route is equal to and of the same type as 'joke/list', include JokeController.php
		//Create instance of jokeController with specific joke and author tables provided above
		//Set $page to JokeController->list
		if ($this->route === 'joke/list') {
			include __DIR__ . '/../classes/controllers/JokeController.php';
			$controller = new JokeController($jokesTable, $authorsTable);
			$page = $controller->list();
		
		//otherwise, if route is equal to and of the same type as '' (i.e. empty), include JokeController.php
		//Create instance of jokeController with specific joke and author tables provided above
		//Set $page to JokeController->home
		} elseif ($this->route === '') {
			include __DIR__ . '/../classes/controllers/JokeController.php';
			$controller = new JokeController($jokesTable, $authorsTable);
			$page = $controller->home();
		
		//otherwise, if route is equal to and of the same type as 'joke/edit', include JokeController.php
		//Create instance of jokeController with specific joke and author tables provided above
		//Set $page to JokeController->edit
		} elseif ($this->route === 'joke/edit') {
			include __DIR__ . '/../classes/controllers/JokeController.php';
			$controller = new JokeController($jokesTable, $authorsTable);
			$page = $controller->edit();
		
		//otherwise, if route is equal to and of the same type as 'joke/delete', include JokeController.php
		//Create instance of jokeController with specific joke and author tables provided above
		//Set $page to JokeController->delete
		} elseif ($this->route === 'joke/delete') {
			include __DIR__ . '/../classes/controllers/JokeController.php';
			$controller = new JokeController($jokesTable, $authorsTable);
			$page = $controller->delete();
			
		//otherwise, if route is equal to and of the same type as 'register', include RegisterController.php
		//Create instance of jokeController with specific joke and author tables provided above
		//Set $page to RegisterController->showform
		} elseif ($this->route === 'register') {
			include __DIR__ . '/../classes/controllers/RegisterController.php';
			$controller = new JokeController($authorsTable);
			$page = $controller->showform();
		}
		
		//This sets the output of this method
		return $page;
	}

	public function run()
	{
		//Define $page as the output of callAction
		$page = $this->callAction();

		//Define $title as whatever is output by the method used above
		$title = $page['title'];
		
		//If $page has defined variables, 
		//pass them to the loadTemplate function (defined above) along with $page['template']
		//$output is used in layout.html.php and sets what is put in the main body of the web page
		if (isset($page['variables'])) {
			$output = $this->loadTemplate($page['template'], $page['variables']);
		//Otherwise, just pass $page['template'] to loadTemplate	
		} else {
			$output = $this->loadTemplate($page['template']);
		}
		
		//This file contains the layout information and uses $title and $output defined above
		include __DIR__ . '/../templates/layout.html.php';
	}
}
