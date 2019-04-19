<?php//This creates the form for adding and editing categories of jokes?>

<form action="" method="post">
	<input 
		type="hidden"
		name="category[id]"
		value="<?=$category->id ?? ''?>"
	/>
	
	<label for="categoryName">Enter category name:</label>
	
	<input 
		type="text"
		id="categoryName"
		value="<?=$category->name ?? ''?>"
	/>
	
	<input
		type="submit"
		name="submit"
		value="Save"
	/>
</form>