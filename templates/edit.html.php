<?php//Only display this edit form if the userId of the logged in user matches the joke's authorId?>
<?php//|| (or) if the jokeid is null, then we're posting a new joke, so anyone can see the edit form?>
<?php//Otherwise, display a message saying they can't edit the joke?>
<?php// echo($userId); echo('shit'); echo(print_r($joke));?>

<?php if (($userId == $joke->authorid) || is_null($joke->authorid)): ?>
	<form action="" method="post">
		<input type="hidden" name="joke[id]" value="<?=$joke->id ?? ''?>">
		<label for="joketext">Type your joke here: </label>
		<textarea id="joketext" name="joke[joketext]"><?=$joke->joketext ?? ''?></textarea>
		<input type="submit" name="submit" value="Save">
	</form>

<?php else: ?>
	<p>You may only edit jokes that you posted</p>
	
<?php endif; ?>
