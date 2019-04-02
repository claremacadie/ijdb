<!-- This file write data to the db obtained from a web form


<?php

// These all need to be filled in correctly!
$user = 'root';
$pwd = 'Snuffle1977';
$db = 'test';

// Connect to MySQL - using this variable is equivalent to logging in manually to the database
$link = mysqli_connect('localhost', $user, $pwd, $db);

//This checks that the variable to log into the database works
if(!$link) {
	echo 'this has failed';
die();
}



?>