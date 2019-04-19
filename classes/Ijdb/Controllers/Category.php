<?php

//This is the category controller

namespace Ijdb\Controllers;

class Category {
	private $categoriesTable;
	
	public function __construct(\Ninja\DatabaseTable $categoriesTable) {
		$this->categoriesTable = $categoriesTable;
	}
	
	//This method finds categories and returns them to be edited
	//If no id is set, then it is a new category that is added to the database
	public function edit() {
		if (isset($GET['id'])) {
			$category = $this->categoriesTable->findById($_GET['id']);
		}
		
		$title = 'Edit Category';
		
		return [
			'template' => 'editcategory.html.php',
			'variables' => ['category' => $category ?? null]
		];
	}
	
	
	
	
	
}