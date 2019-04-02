<?php
try {
echo ('hi');

	include __DIR__ . '/../includes/DatabaseConnection.php';
echo ('hi2');
	include __DIR__ . '/../classes/DatabaseTable.php';
echo ('hi3');
	include __DIR__ . '/../controllers/JokeController.php';
echo ('hi4');

	$jokesTable = new DatabaseTable($pdo, 'joke', 'id');
	$authorsTable = new DatabaseTable($pdo, 'author', 'id');
	
	$jokeController = new JokeController($jokesTable, $authorsTable);
	
	if (isset($_GET['edit'])) {
		$page = $jokeController->edit();
	} elseif (isset($_GET['delete'])) {
		$page = $jokeController->delete();
	} elseif (isset($_GET['list'])) {
		$page = $jokeController->list();
	} else {
		$page = $jokeController->home();
	}
	
	$title = $page['title'];
	$output = $page['output'];
} 	
//If $pdo doesn't work, this provides an error message
	catch (PDOException $error) {
	$title = 'An error has occurred';
	
	$output = 'Unable to connect to the database server: ' . 
		$error->getMessage() . ' in ' .
		$error->getFile() . ':' . $error->getLine();
}

//This files contains the layout information
include __DIR__ . '/../templates/layout.html.php';