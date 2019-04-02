<?php
//If something has been entered into the text box and the button has been clicked, then there will be text in $_POST['joketext']
//DatabaseConnection.php sets up the connection to the database
//DatabaseFunctions.php contains functions, including insert

if (isset($_POST['joketext'])) {
	try {
		include __DIR__ . '/../includes/DatabaseConnection.php';
		include __DIR__ . '/../includes/DatabaseFunctions.php';
		
		//insert is defined in DatabaseFunctions.php
		save($pdo, 'joke', 'id', [
			'authorId' => 1,
			'joketext' => $_POST['joketext'],
			'jokedate' => new DateTime()
		]);
			
		
	//Send the browser to jokes.php
	header('location: jokes.php');
	
	//If $pdo doesn't work, this provides an error message
	} catch (PDOException $error) {
		$title = 'An error has occurred';
	
		$output = 'Unable to connect to the database server: ' . 
			$error->getMessage() . ' in ' .
			$error->getFile() . ':' . $error->getLine();
		}

	//If nothing has yet been entered into the text box, it uses the addjoke.html.php file
	} else {
		//Set variable 'title' for use in the include file
		$title = 'Add a new joke';
		
		//ob_start starts a buffer that gets filled by the include file and then output to the website at the end
		ob_start();
		include __DIR__ . '/../templates/addjoke.html.php';
		$output = ob_get_clean();
}
//This files contains the layout information
include __DIR__ . '/../templates/layout.html.php';