<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Test page</title>
	</head>
	<body>

<?php
 
for ($count = 1; $count <= 10; $count++) {
	$roll = rand(1, 6);
	echo '<p>You rolled a ' . $english[$roll] . '</p>';
	
	if ($roll == 6) {
		echo '<p>You win!</p>';
	}
	else {
		echo '<p>Sorry, you didn\'t win, better luck next time!</p>';
	}
}

echo '<p>Thanks for playing</p>';
	
?>	
	</body>
</html>