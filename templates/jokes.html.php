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
		
		<!If there are actions edit and id posted (_POST or _GET) to the website, this is passed to index.php>
		<!index.php uses these actions to load the correct template using the loadTemplate function>
		<a href ="index.php?action=edit&id=<?=$joke['id']?>">
		Edit
		</a>
		
		<!If the action delete is posted (_POST or _GET) to the website, this is passed to index.php>
		<!index.php uses this action to load the correct template using the loadTemplate function>
		<form action="index.php?action=delete" method="post">
		<input type="hidden" name="id" value="<?=$joke['id']?>">
			<input type="submit" value="Delete">
		</form>
	</p>
</blockquote>
<?php endforeach; ?>

