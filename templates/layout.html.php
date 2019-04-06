<!doctype html>
<!This files sets the layout for the jokedatabase website using the jokes.css file>
<!The action=list/edit is passed to index.php to use in the loadTemplate function>
<html>
	<head>
	  <meta charset="utf-8">
	  <link rel="stylesheet" href="jokes.css">
	  <title><?=$title?></title>
	</head>
	<body> 
		<header>
			<h1> Internet Joke Database</h1>
		</header>
		<nav>
			<ul>
				<li><a href="index.php">Home</a></li>
				<li><a href="index.php?route=joke/list">Jokes list</a></li>
				<li><a href="index.php?route=joke/edit">Add a new joke</a></li>
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