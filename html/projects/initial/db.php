<?php

// A really simple example of how PHP connects to a database, runs a query 
//and then deals with the result

// These all need to be filled in correctly!
$user = 'root';
$pwd = 'Snuffle1977';
$db = 'test';

// Connect to MySQL - this is equivalent to logging in manually
$link = mysqli_connect('localhost', $user, $pwd);
if (!$link)
{
  $error = 'Unable to connect to the database server';
  include 'error.php';
  die();
}

// Set the charset to utf8
if (!mysqli_set_charset($link, 'utf8'))
{
  $error = 'Unable to set database connection encoding';
  include 'error.php';
  die();
}

// Set connection to the database
if (!mysqli_select_db($link, $db))
{
  $error = 'Unable to locate the database';
  include 'error.php';
  die();
}

// Make sure submitted data is clean
// If you ever inject paramters into SQL the paramters need
// to be cleaned as this is a source of what's called injection
// attacks
$studentID = 1;
$studentID = mysqli_real_escape_string($link, $studentID);

// Query to pull student data from DB
// ... can use a stored procedure on the database as well
$sql = "SELECT
      s.`StudentFirstName`
	FROM
		`students` s
	WHERE
		s.StudentID = " . $studentID . ";";

// Run query and handle any failure
$result = mysqli_query($link, $sql);
if (!$result) {
  $error = 'Error fetching student: <br />' . mysqli_error($link) . '<br /><br />' . $sql;
  include 'error.php';
  die();
}

// Store results
$row = mysqli_fetch_assoc($result);
$victim = $row['StudentFirstName'];