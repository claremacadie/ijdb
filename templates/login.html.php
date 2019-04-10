<?php//This file creates the webpage for logging in_array?>

<?php
//This displays an error message in case logging in is not successful
if (isset($error)) {
	echo '<div class="errors">' . $error . '</div>';
}
?>

<form method="post" action="">
	<label for="email">Your email address</label>
	<input type="text" id="email" name="email">
	
	<label for="password">Your password</label>
	<input type="password" id="password" name="password">
	
	<input type="submit" name="login" value="Log in">
</form>

<p>Don't have an account? 
	<a href="\author\register">Click here to register an account</a>
</p>