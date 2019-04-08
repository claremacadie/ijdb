<?php
//This files creates a class 'Joke' with inputs of tables '$jokesTable' and 'authorsTable'
//Joke controller is in namespace Ijdb\Controllers
//All of the methods (functions) in this file are then available to instances of this controller created by other files


//namespace is like a folder and gives classes unique names, in case another developed creates an EntryPoint class
namespace Ijdb\Controllers;

//Although we are in Ijdb\Controllers namespace, 
//'use' tells this file to look in namespace \Ninja\DatabaseTable for classes it can't find in Ijdb\Controllers
use \Ninja\DatabaseTable;


class Joke {
	private $jokesTable;
	private $authorsTable;
	
	//This constructs JokeController, with the jokesTable and authorsTable 
	//When a JokeController class is created, __construct tells it that 
	//$jokesTable is an input and it must be a DatabaseTable, and
	//$authorsTable is an input and it must be a DatabaseTable
	
	public function __construct(DatabaseTable $jokesTable, DatabaseTable $authorsTable) {
		$this->jokesTable = $jokesTable;
		$this->authorsTable = $authorsTable;
	}	

	//Use the FindAll function (defined in DatabaseTable.php) to return a list of all the jokes in the database
	public function list() {
		$result = $this->jokesTable->findAll();
		
		//Create an array ($jokes) for jokes.html.php to iterate to produce the list of jokes
		$jokes = [];
		foreach ($result as $joke) {
			$author = $this->authorsTable->findById($joke['authorid']);
			
			$jokes[] = [
				'id' => $joke['id'],
				'joketext' => $joke['joketext'],
				'jokedate' => $joke['jokedate'],
				'name' => $author['name'],
				'email' => $author['email']
			];
		}
		
		//Set variable 'title' for use in the include file
		$title = 'Joke list';
		
		//Use total (defined in DatabaseFunctions.php) to return the total number of jokes
		$totalJokes = $this->jokesTable->total();
		
		//These variables are passed back to the file using JokeController
		return [
		'template' => 'jokes.html.php', 
		'title' => $title, 
		'variables' => ['totalJokes' => $totalJokes, 'jokes' => $jokes]
		];

	}
	
	//Sends the browser to the home page
	public function home() {
		$title = 'Internet Joke Database';
		
		return ['template' => 'home.html.php', 'title' => $title];
	}

	//Deletes joke with matching id and sends the browser to the jokes list page
	public function delete() {
		$this->jokesTable->delete($_POST['id']);
		
		//Send the browser to /joke/list
		//Because the directory joke/list does not exist on the server, .htaccess redirects this url to index.php
		header('location: /joke/list');
		
	}
	
	//When something has been entered (_POST) into the text box...
	//This function converts DateTime objects to a string that MySQL understands
	//DateTime has a '\' in front because we are in Ijdb/Controllers namespace
	//and DateTime is an in-built PHP class, in the global namespace
	//'\' tells it to start from global namespace
	public function saveEdit() {
		$joke = $_POST['joke'];
		$joke['jokedate'] = new \DateTime();
		$joke['authorId'] = 1;
			
		//save is defined in DatabaseTable.php
		$this->jokesTable->save($joke);
			
		// Set these to stop PHP compile warning in error log
		$title = '';
		$output = '';
			
		//Send the browser to /joke/list
		//Because the directory joke/list does not exist on the server, .htaccess redirects this url to index.php
		header('location: /joke/list');
	}

		
	//When nothing has yet been entered into the text box, this function retrieves the joke to be edited
	//It pastes the text of the joke in the form so it can be edited
	//findById is defined in DatabaseTable.php
	public function edit(){
		if (isset($_GET['id'])) {
			$joke = $this->jokesTable->findById($_GET['id']);
		}
		
		//Set variable 'title' for use in the include file
		$title = 'Edit joke';
			
		return [
		'template' => 'edit.html.php', 
		'title' => $title,
		'variables' => ['joke' => $joke ?? null]
		];

	}
}