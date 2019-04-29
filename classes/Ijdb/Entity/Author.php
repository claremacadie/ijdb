<?php

//This file creates an entity class 'Author'
//This enables methods to be called on the $author instance, e.g. $author->addJoke($joke);
//For this to work, $author needs to be an object, rather than an array, which is what this file does

namespace Ijdb\Entity;

class Author {
	public $id;
	public $name;
	public $email;
	public $password;
	private $jokesTable;
	
	//Define the constants for user permissions
	const EDIT_JOKES = 1;
	const DELETE_JOKES = 2;
	const LIST_CATEGORIES = 4;
	const EDIT_CATEGORIES = 8;
	const REMOVE_CATEGORIES = 16;
	const EDIT_USER_ACCESS = 32;	
	
	//Create a DatabaseTable object called $jokesTable
	public function __construct(\Ninja\DatabaseTable $jokesTable) {
		$this->jokesTable = $jokesTable;
	}
	
	//This method retrieves jokes from the database where the authorId matches the id of this Author class
	public function getJokes() {
		return $this->jokesTable->find('authorId', $this->id);
	}
	
	//This method adds jokes to the database using the save method (defined in DatabaseTable)
	//It sets the authorId of the joke to be added as the id of this Author class
	//and return enables the value of the save method to be output when this method is used
	public function addJoke($joke) {
		$joke['authorId'] = $this->id;
		return $this->jokesTable->save($joke);
	}
	
	//This method checks if a user has a specific permissions
	public function hasPermission($permission) {
		return $this->permissions & $permission;
	}
}