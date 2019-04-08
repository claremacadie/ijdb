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
	public function registerUser()
	{	
		$author = $_POST['author'];
		
		$this->authorsTable->save($author);
		
		header('Location: /author/success');
	}
}