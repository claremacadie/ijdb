<?php//doctype html?>
<?php//This files sets the layout for the jokedatabase website using the jokes.css file?>
<?php//Make sure jokes.css is in the same directory as index.php?>
<html>
	<head>
	  <meta charset="utf-8">
	  <link rel="stylesheet" href="/jokes.css">
	  <title><?=$title?></title>
	</head>
	<body> 
		<header>
			<h1> Internet Joke Database</h1>
		</header>
		<nav>
			<ul>
				<li><a href="/">Home</a></li>
				<li><a href="/joke/list">Jokes list</a></li>
				<li><a href="/joke/edit">Add a new joke</a></li>
				
				<?php//This displays a logout option when a user is logged in and and login option when they are not logged in?>
				<?php if ($loggedIn): ?>
				<li><a href="/logout">Log out</a></li>
				<?php else: ?>
				<li><a href="/login">Log in</a></li>
				<?php endif; ?>
	
			</ul>
		</nav>
		
		<main>
			<?=$output?>
		</main>
		
		<footer>
			&copy; IJDB 2019
		</footer>	
	</body>

</html>