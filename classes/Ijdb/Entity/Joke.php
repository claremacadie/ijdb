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
	private $jokeCategoriesTable;
	
	public function __construct(\Ninja\DatabaseTable $authorsTable, \Ninja\DatabaseTable $jokeCategoriesTable) {
		$this->authorsTable = $authorsTable;
		$this->jokeCategoriesTable = $jokeCategoriesTable;
	}
	
	//Returns the author for the current joke
	public function getAuthor() {
		if (empty($this->author)) {
			$this->author = $this->authorsTable->findById($this->authorId);
		}
		return $this->author;
	}
	
	//Whenever a joke is added to the website, it is assigned to the categories that were checked
	public function addCategory($categoryId) {
		$jokeCat = [
			'jokeId' => $this->id,
			'categoryId' => $categoryId
		];
		
		$this->jokeCategoriesTable->save($jokeCat);
	}
}
