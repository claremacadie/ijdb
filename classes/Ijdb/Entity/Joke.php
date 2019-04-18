<?php
//This creates a Joke entity class and its methods are available to joke objects

namespace Ijdb\Entity;

class Joke {
	public $id;
	public $authorId;
	public $jokeDate;
	public $jokeText;
	private $authorsTable;
	
	public function __construct(\Ninja\DatabaseTable $authorsTable) {
		$this->authorsTable = $authorsTable;
	}
	
	//Returns the author for the current joke
	public function getAuthor() {
		return $this->authorsTable->findById($this->authorId);
	}
}
