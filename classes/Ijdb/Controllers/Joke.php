<?php
//This files creates a class 'Joke' with inputs of tables '$jokesTable' and 'authorsTable'
//Joke controller is in namespace Ijdb\Controllers
//All of the methods (functions) in this file are then available to instances of this controller created by other files


//namespace is like a folder and gives classes unique names, in case another developed creates an EntryPoint class
namespace Ijdb\Controllers;

//Although we are in Ijdb\Controllers namespace, 
//'use' tells this file to look in namespaces \Ninja\DatabaseTable and Authentication for classes it can't find in Ijdb\Controllers
use \Ninja\DatabaseTable;
use \Ninja\Authentication;


class Joke {
	private $jokesTable;
	private $authorsTable;
	
	//This constructs JokeController, with the jokesTable and authorsTable 
	//When a JokeController class is created, __construct tells it that 
	//$jokesTable is an input and it must be a DatabaseTable, and
	//$authorsTable is an input and it must be a DatabaseTable, and
	//$authentication is an input and it must be an Authentication object
	
	public function __construct(DatabaseTable $jokesTable, DatabaseTable $authorsTable, Authentication $authentication) {
		$this->jokesTable = $jokesTable;
		$this->authorsTable = $authorsTable;
		$this->authentication = $authentication;
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
				'email' => $author['email'],
				'authorId' => $author['id']
			];
		}
		
		//Set variable 'title' for use in the include file
		$title = 'Joke list';
		
		//Use total (defined in DatabaseFunctions.php) to return the total number of jokes
		$totalJokes = $this->jokesTable->total();
		
		//Get the currently logged in user
		$author = $this->authentication->getUser();
		
		//These variables are output when this method is used
		//if there is no $author['id'] (because no user is logged in), 'userId' is set to null
		//echo($author['id']);
		//die;
		return [
			'template' => 'jokes.html.php', 
			'title' => $title, 
			'variables' => [
				'totalJokes' => $totalJokes, 
				'jokes' => $jokes, 
				'userId' => $author['id'] ?? null
			]
		];

	}
	
	//Sends the browser to the home page
	public function home() {
		$title = 'Internet Joke Database';
		
		return ['template' => 'home.html.php', 'title' => $title];
	}

	//Deletes joke with matching id and sends the browser to the jokes list page
	public function delete() {
		
		//Set $author to the logged in user
		$author = $this->authentication->getUser();
		
		//Set $joke to the joke in the databae matching the id, using findById
		$joke = $this->jokesTable->findById($_POST['id']);
		
		//If the authorId of the joke does not match the author['id'] of the user
		//return leaves this method so that the code below is not executed and the 
		//joke is not deleted the database
		if ($joke['authorid'] != $author['id']) {
			return;
		}
		
		//Otherwise, delete the joke from the database
		$joke = $this->jokesTable->delete($_POST['id']);
		
		//Send the browser to /joke/list
		header('location: /joke/list');
		
		//This stops the current code path because this method does not return a template and title, so
		//when it goes back to EntryPoint.php there is nothing to process in run(), which elicits an error
		//The code path has been taken by the header command above anyhow
		die();
	}
	
	//This function saves changes to the joke database
	public function saveEdit() {
	
		//This sets $author to the logged in user
		$author = $this->authentication->getUser();
		
		//If the id is set, get the joke from the database using findById
		if (isset($_GET['id'])) {
			$joke = $this->jokesTable->findById($_GET['id']);
			
			//If the authorId of the joke does not match the author['id'] of the user
			//return leaves this method so that the code below is not executed and the 
			//changes are not saved to the database
			if ($joke['authorid'] != $author['id']) {
				return;
			}
		}
			
		//Set $joke to the text posted
		$joke = $_POST['joke'];
		
		//This converts DateTime objects to a string that MySQL understands
		//DateTime has a '\' in front because we are in Ijdb/Controllers namespace
		//and DateTime is an in-built PHP class, in the global namespace
		//'\' tells it to start from global namespace
		$joke['jokedate'] = new \DateTime();
		
		//Set the authorid of the joke to be the id of the author that is logged in
		$joke['authorId'] = $author['id'];
		//echo(print_r($joke));
		//echo(print_r($author));
		//die();		
		//save is defined in DatabaseTable.php
		$this->jokesTable->save($joke);
			
		// Set these to stop PHP compile warning in error log
		$title = '';
		$output = '';
			
		//Send the browser to /joke/list
		header('location: /joke/list');
		
		//This stops the current code path because this method does not return a template and title, so
		//when it goes back to EntryPoint.php there is nothing to process in run(), which elicits an error
		//The code path has been taken by the header command above anyhow
		die();
	}

		
	//When nothing has yet been entered into the text box, this function retrieves the joke to be edited
	//It pastes the text of the joke in the form so it can be edited
	//findById is defined in DatabaseTable.php
	public function edit(){
		
		//Set $author to the logged in user
		$author = $this->authentication->getUser();
		//echo($author['id']);
		if (isset($_GET['id'])) {
			$joke = $this->jokesTable->findById($_GET['id']);
		}
		
		//Set variable 'title' for use in the include file
		$title = 'Edit joke';
		//echo(print_r($author['id']));
		return [
			'template' => 'edit.html.php', 
			'title' => $title,
			'variables' => [
				'joke' => $joke ?? null,
				'userId' => $author['id'] ?? null
			]	
		];

	}
}