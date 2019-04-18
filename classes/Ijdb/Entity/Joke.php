<?php
//This creates a Joke entity class and its methods are available to joke objects

namespace Ijdb\Entity;

class Joke {
	public $id;
	public $authorId;
	public $jokeDate;
	public $jokeText;
	private $authorsTable;
	private $author;
	
	public function __construct(\Ninja\DatabaseTable $authorsTable) {
		$this->authorsTable = $authorsTable;
	}
	
	//Returns the author for the current joke
	public function getAuthor() {
		if (empty($this->author)) {
			$this->author = $this->authorsTable->findById($this->authorId);
		}
		return $this->author;
	}
}
