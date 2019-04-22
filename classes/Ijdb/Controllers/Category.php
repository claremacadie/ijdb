<?php

//This is the category controller

namespace Ijdb\Controllers;

class Category {
	private $categoriesTable;
	
	public function __construct(\Ninja\DatabaseTable $categoriesTable) {
		$this->categoriesTable = $categoriesTable;
	}
	
	//If an id is set, this method finds the category in the database and returns it to the form to be edited
	//If no id is set, then the form is blank
	public function edit() {
		if (isset($_GET['id'])) {
			$category = $this->categoriesTable->findById($_GET['id']);
		}
		
		$title = 'Edit Category';
		
		return [
			'template' => 'editcategory.html.php',
			'title' =>$title,
			'variables' => ['category' => $category ?? null]
		];
	}
	
	//This method uses the DatabaseTable save method to update existing categories and insert new categories
	public function saveEdit() {
		$category = $_POST['category'];
		$this->categoriesTable->save($category);
		
		//Redirect browser to category/list webpage
		header('location: /category/list');
		
		//End this program flow to prevent PHP warning in error log
		die();

	}	

	//This function lists the categories and the template enables them to be edited and deleted
	public function list() {
		$categories = $this->categoriesTable->findAll();
		$title = 'Joke Categories';
		return [
			'template' => 'categories.html.php',
			'title' => $title,
			'variables' => ['categories' => $categories]
		];
	}
			
	//This function enables categories to be deleted
	public function delete() {
		$this->categoriesTable->delete($_POST['id']);
		
		//redirects the browser to the category/list page
		header('location: /category/list');
		
		//End this program flow to prevent PHP warning in error log
		die();
	}
	
	//This function gets jokes from the database and uses usort, which
	//takes two arguments, an array to be sorted and the function that compares two values (sortJokes, defined below)
	//WTF see page 646
	public function getjokes() {
		$jokeCategories = $this->jokeCategoriesTable->find('categoryId', $this->id);
		$jokes = [];
		
		foreach ($jokeCategories as $jokeCategory) {
			$joke = $this->jokesTable->findById($jokeCategory->jokeId);
			
			if ($joke) {
				$jokes[] = $joke;
			}
		}
		
		usort($jokes, [$this, 'sortJokes']);
		
		return $jokes;
	}
	
	//This function sorts jokes by date
	private function sortJokes($a, $b) {
		$aDate = new \DateTime($a->jokeDate);
		$bDate = new \DateTime($b->jokeDate);
		
		if ($aDate->getTimestamp() == $bDate->getTimestamp()) {
			return 0;
		}
		//if $a>$b output -1, else output 1
		return $aDate->getTimestamp() > $bDate->getTimestamp() ? -1 : 1;
	}
}