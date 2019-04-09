<?php
//This file creates the Register class
//This class displays a form for authors to input their details and display a 'registration successful' page

//namespace is like a folder and gives classes unique names, in case another developed creates an EntryPoint class
namespace Ijdb\Controllers;

//Although we are in Ijdb\Controllers namespace, 
//'use' tells this file to look in namespace \Ninja\DatabaseTable for classes it can't find in Ijdb\Controllers
use \Ninja\DatabaseTable;

//Create Register class
class Register
{
	//Declare $authorsTable as a variable for the class
	private $authorsTable;
	
	//When a register class is created, __construct tells it that 
	//$authorsTable is an input and it must be a DatabaseTable
	public function __construct(DatabaseTable $authorsTable)
	{
		$this->authorsTable= $authorsTable;
	}
	
	//This function creates a registration form using register.html.php
	public function registrationForm()
	{
		return ['template' => 'register.html.php', 'title' => 'Register an account'];
	}
	
	//This function displays the registration successful page using registersuccess.html.php
	public function success()
	{
		return ['template' => 'registersuccess.html.php', 'title' => 'Registration Successful'];
	}
	
	//This function registers users and displays the successful registration page
	//This contains validation that the fields are not left blank
	//Validation also includes that a valid email address has been entered that is not already in the database
	public function registerUser()
	{	
		$author = $_POST['author'];
		
		//Assume the data is valid to begin with
		$valid = true;
			
		//Create an array to store a list of error messages
		$errors = [];		
			
		//But if any of the fields have been left blank set $valid to false
		//$errors[] = means each error will be added to the end of the errors array so
		//all the error messages will be stored in $errors
		if (empty($author['name'])) {
			$valid = false;
			$errors[] = 'Name cannot be blank';
		}
		
		if (empty($author['email'])) {
			$valid = false;
			$errors[] = 'Email cannot be blank';
		}	
		
		//This check uses an inbuilt function to check for valid email addresses
		else if (filter_var($author['email'], FILTER_VALIDATE_EMAIL) == false) {
			$valid = false;
			$errors[] = 'Invalid email address';
		}
		
		if (empty($author['password'])) {
			$valid = false;
			$errors[] = 'Password cannot be blank';
		}
		
		//If valid is still true, no fields were blank and the data can be added
		if ($valid == true) {
			$this->authorsTable->save($author);
				
			header('Location: /author/success');
		}
		else {
			//If the data is not valid, display the errors and show the form again
			return [
				'template' => 'register.html.php', 
				'title' => 'Register an account',
				'variables' => ['errors' => $errors,'author' => $author]
			];
		}
	}
}