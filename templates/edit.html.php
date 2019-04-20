<?php//Only display this edit form if the userId of the logged in user matches the joke's authorId?>
<?php//|| (or) if the jokeid is null, then we're posting a new joke, so anyone can see the edit form?>
<?php//Otherwise, display a message saying they can't edit the joke?>

<?php if (empty($joke->id) || $userId == $joke->authorId):?>
	<form 
		action="" 
		method="post"
	/>
		
		<input 
			type="hidden" 
			name="joke[id]" 
			value="<?=$joke->id ?? ''?>"
		/>
		
		<label for="jokeText">Type your joke here: </label>
		
		<textarea 
			id="jokeText" 
			name="joke[jokeText]"><?=$joke->jokeText ?? ''?>
		</textarea>
	
		<p>Select categories for this joke:</p>
		<?php//Loop over each category?>
		<?php foreach ($categories as $category): ?>

			<?php//create a checkbox for each category with the value property set to the id of the category?>
			<?php//category[] creates an array when the form is submitted to store the category names?>
			<?php//use the hasCategory method in the joke entity to check the boxes for relevant categories?>
			<?php//&& means if $joke is set and hasCategory is true?>
			<?php if ($joke && $joke->hasCategory($category->id)): ?>
		
				<?php//create a checkbox for each category with the value property set to the id of the category?>
				<?php//category[] creates an array when the form is submitted to store the category names?>
				<input	
					type="checkbox"
					checked name="category[]"
					value="<?=$category->id?>"
				/>
				
			<?php else: ?>
				<input	
					type="checkbox"
					name="category[]"
					value="<?=$category->id?>"
				/>
							
			<?php endif;?>
			
			<label><?=$category->name?></label>
			
		<?php endforeach; ?>
		
		<input 
			type="submit" 
			name="submit" 
			value="Save"
		/>
	</form>

<?php else: ?>
	<p>You may only edit jokes that you posted</p>
	
<?php endif; ?>
