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
	
	//This method returns the author for the current joke
	public function getAuthor() {
		if (empty($this->author)) {
			$this->author = $this->authorsTable->findById($this->authorId);
		}
		return $this->author;
	}
	
	//This method is used to ensure whenever a joke is added to the website, it is assigned to the categories that were checked
	public function addCategory($categoryId) {
		$jokeCat = [
			'jokeId' => $this->id,
			'categoryId' => $categoryId
		];
		
		$this->jokeCategoriesTable->save($jokeCat);
	}
	
	//This method determines whether a joke has a category assigned
	//It loops through the categories and checks to see if each one matches a given $categoryId
	public function hasCategory($categoryId) {
		$jokeCategories = $this->jokeCategoriesTable->find('jokeId', $this->id);
		foreach ($jokeCategories as $jokeCategory) {
			if ($jokeCategory->categoryId == $categoryId) {
				return true;
			}
		}
	}
	
	//This method removes all category assignments for a particular joke from the joke_category table
	//This is used to clear all categories before adding categories back in when editing jokes
	//(it's easier than looping through each category to see if it is checked and unchecking if needed)
	public function clearCategories() {
		$this->jokeCategoriesTable->deleteWhere('jokeId', $this->id);
	}	
}