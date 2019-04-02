<?php
//If something has been entered into the text box and the button has been clicked, then there will be text in $_POST['joketext']
//DatabaseConnection.php sets up the connection to the database
//DatabaseTable.php contains functions, including save and findById
//echo ("echo");

try {

	include __DIR__ . '/../includes/DatabaseConnection.php';
	include __DIR__ . '/../classes/DatabaseTable.php';

	$jokesTable = new DatabaseTable($pdo, 'joke', 'id');

	//If something has been entered into the text box...
	if (isset($_POST['joke'])) {
		$joke = $_POST['joke'];
		$joke['jokedate'] = new DateTime();
		$joke['authorId'] = 1;
		
		//save is defined in DatabaseFunctions.php
		$jokesTable->save($joke);
		
		// Set these to stop PHP compile warning in error log
		$title = '';
		$output = '';
		
		//Send the browser to jokes.php
		header('location: jokes.php');
	
	//If nothing has yet been entered into the text box, it retrieves the joke to be edited
	//findById is defined in DatabaseFunctions.php
	} else {
		if (isset($_GET['id'])) {
			$joke = $jokesTable->findById($_GET['id']);
		}
		
		//Set variable 'title' for use in the include file
		$title = 'Edit joke';
		
		//ob_start starts a buffer that gets filled by the include file and then output to the website at the end
		ob_start();
		include __DIR__ . '/../templates/editjoke.html.php';
		$output = ob_get_clean();
	}
		
//If $pdo doesn't work, this provides an error message
} catch (PDOException $error) {
		$title = 'An error has occurred';
	
		$output = 'Unable to connect to the database server: ' . 
			$error->getMessage() . ' in ' .
			$error->getFile() . ':' . $error->getLine();
		
}
//This files contains the layout information
include __DIR__ . '/../templates/layout.html.php';