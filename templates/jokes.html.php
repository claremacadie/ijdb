<?php //This file creates the html code for the jokes list page on the website?>

<?php //Outputs the total number of jokes?>
<p><?=$totalJokes?> jokes have been submitted to the Internet Joke Database.</p>

<?php //Outputs a list of jokes with an email link for the author, date (formatted to 1st april 2019), edit link and delete button?>
<?php foreach ($jokes as $joke): ?>
<blockquote>
	<p>
		<?=htmlspecialchars($joke['joketext'], ENT_QUOTES, 'UTF-8')?>
		
		(by 
		<a href="mailto:<?php echo htmlspecialchars($joke['email'], ENT_QUOTES, 'UTF-8'); ?>">
		<?=htmlspecialchars($joke['name'], ENT_QUOTES, 'UTF-8'); ?>
		</a> 
		
		on 
		
		<?php
		$date = new DateTime($joke['jokedate']);
		echo $date->format('jS F Y');
		?>)
		
		<?php//When a user is logged in, if their userId matches the authorId of a joke, the edit and delete actions are available?>
		<?php//Otherwise, just the joke is listed?>	
		<?php if ($userId == $joke['authorId']): ?>
			<a href ="/joke/edit?id=<?=$joke['id']?>">
			Edit
			</a>
		
			<form action="/joke/delete" method="post">
				<input type="hidden" name="id" value="<?=$joke['id']?>">
				<input type="submit" value="Delete">
			</form>
		
		<?php endif; ?>
		
	</p>
</blockquote>
<?php endforeach; ?>

