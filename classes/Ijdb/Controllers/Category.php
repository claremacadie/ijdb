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
		header('location: /category/list');
	}	
	
	
}