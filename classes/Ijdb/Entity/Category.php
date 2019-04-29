<?php
//This creates a category entity with associated methods

namespace Ijdb\Entity;

use Ninja\DatabaseTable;

class Category {
	public $id;
	public $name;
	private $jokesTable;
	private $jokeCategoriesTable;
	
	//Construct the jokesTable and jokeCategoriesTable
	public function __construct(DatabaseTable $jokesTable, DatabaseTable $jokeCategoriesTable) {
		$this->jokesTable = $jokesTable;
		$this->jokeCategoriesTable = $jokeCategoriesTable;
	}
	
	//This method returns the first 10 jokes matching a particular category id
	public function getJokes($limit = null, $offset = null) {
		$jokeCategories = $this->jokeCategoriesTable->find('categoryId', $this->id, null, $limit, $offset);
		$jokes = [];
		foreach ($jokeCategories as $jokeCategory) {
			$joke = $this->jokesTable->findById($jokeCategory->jokeId);
			if ($joke) {
				$jokes[] = $joke;
			}
		}
		return $jokes;
	}

	//This method returns the total number of jokes for a given category
	public function getNumJokes() {
		return $this->jokeCategoriesTable->total('categoryId', $this->id);
	}
}