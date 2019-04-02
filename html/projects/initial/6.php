<!-- This file copies the diet table from the database and pastes it onto the website.-->

hello <br> <!-- <br> is a page break -->

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

//This pulls out data from the database
$sql = "SELECT * FROM diet";
$result = mysqli_query ($link,$sql);

//fetch_assoc iterates to bring first row of a table the first time it is used, 
    $row = mysqli_fetch_assoc($result);
	
//echo prints the data to the website
    echo "id: ". $row["DietId"]. " Name: " . $row["DietName"] . "<br>";
		
//this is the second time fetch_assoc is called so it brings back the second row
	$row = mysqli_fetch_assoc($result); 
    echo "id: ". $row["DietId"]. " Name: " . $row["DietName"] . "<br>";

//this is the third time fetch_assoc is called so it brings back the third row
	$row = mysqli_fetch_assoc($result); 
    echo "id: ". $row["DietId"]. " Name: " . $row["DietName"] . "<br>";

?>