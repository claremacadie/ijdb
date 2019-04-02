<?php
try {
	$pdo = new PDO('mysql:host=localhost;dbname=ijdb;
	charset=utf8', 'ijdbuser', 'ijdb2019%^&');
	$pdo->setAttribute(PDO::ATTR_ERRMODE,
		PDO::ERRMODE_EXCEPTION);
		
	$sql = 'UPDATE joke SET jokedate="2012-04-01"
		WHERE joketext LIKE "%programmer%"';
		
		$affectedRows = $pdo->exec($sql);
	
	$output = 'Updated ' . $affectedRows . ' rows.';

} catch (PDOException $e) {
	$output = 'Database error: ' . 
		$e->getMessage() . ' in ' .
		$e->getFile() . ':' . $e->getLine();
}

include __DIR__ . '/../templates/output.html.php';