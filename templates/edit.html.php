<form action="" method="post">
	<input type="hidden" name="joke[id]" value="<?=$joke['id'] ?? ''?>">
	<label for="joketext">Type your joke here: </label>
	<textarea id="joketext" name="joke[joketext]"><?=$joke['joketext'] ?? ''?></textarea>
	<input type="submit" name="submit" value="Save">

</form>