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
	
	//This method determines whether a joke has a category assigned
	//It loops through the categories and checks to see each one matches a given $categoryId
	public function hasCategory($categoryId) {
		$jokeCategories = $this->jokeCategoriesTable->find('jokeId', $this->id);
		foreach ($jokeCategories as $jokeCategory) {
			if ($jokeCategory->categoryId == $categoryId) {
				return true;
			}
		}
	}
	
}
