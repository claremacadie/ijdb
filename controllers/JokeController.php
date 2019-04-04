<?php
class JokeController {
	private $authorsTable;
	private $jokesTable;
		
	public function __construct(DatabaseTable $jokesTable, DatabaseTable $authorsTable) {
		$this->jokesTable = $jokesTable;
		$this->authorsTable = $authorsTable;
	}	

//Use the FindAll function (defined in DatabaseFunctions.php) to return a list of all the jokes in the database
public function list() {
	$result = $this->jokesTable->findAll();
	
	//Create an array ($jokes) for jokes.html.php to iterate to produce the list of jokes
	$jokes = [];
	foreach ($result as $joke) {
		//This line doesn't work
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
	
	return [
	'template' => 'jokes.html.php', 
	'title' => $title, 
	'variables' => ['totalJokes' => $totalJokes, 'jokes' => $jokes]
	];

}




public function home() {
	$title = 'Internet Joke Database';
	
	return ['template' => 'home.html.php', 'title' => $title];
}


public function delete() {
	$this->jokesTable->delete($_POST['id']);
	
	//Send the browser to jokes.php
	header('location: index.php?action=list');
	
}

public function edit() {
	//If something has been entered into the text box...
	if (isset($_POST['joke'])) {
		$joke = $_POST['joke'];
		$joke['jokedate'] = new DateTime();
		$joke['authorId'] = 1;
		
		//save is defined in DatabaseFunctions.php
		$this->jokesTable->save($joke);
		
		// Set these to stop PHP compile warning in error log
		$title = '';
		$output = '';
		
		//Send the browser to jokes.php
		header('location: index.php?action=list');
	
	//If nothing has yet been entered into the text box, it retrieves the joke to be edited
	//findById is defined in DatabaseFunctions.php
	} else {
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


}