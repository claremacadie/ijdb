<?php

try {
	$pdo = new PDO('mysql:host=localhost;dbname=ijdb;
	charset=utf8', 'ijdbuser', 'ijdb2019%^&');
	$pdo->setAttribute(PDO::ATTR_ERRMODE,
		PDO::ERRMODE_EXCEPTION);
		
	$sql = 'SELECT `joketext` FROM `joke`';
	$result = $pdo->query($sql);
	
	while ($row = $result->fetch()) {
			$jokes[] = $row['joketext'];
			
	$title = 'Joke list';
	
	$output = include 'jokes.html.php';
	
	foreach ($jokes as $joke) {
		$output .= '<blockquote class="blockquote">';
		$output .= '<p>';
		$output .= $joke;
		$output .= '</p>';
		$output .= '</blockquote>';
		}
	}

} catch (PDOException $error) {
	$title = 'An error has occurred';
	
	$output = 'Unable to connect to the database server: ' . 
		$error->getMessage() . ' in ' .
		$error->getFile() . ':' . $error->getLine();
}

include __DIR__ . '/../templates/layout.html.php';
//include __DIR__ . '/../templates/jokes.html.php';


