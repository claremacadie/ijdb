<?php
	// As with last example we're outsourcing the assigment of value 
	// to $victim to another PHP file
	include 'db.php';
?>
<html>
<head>
	<title>Clare's Database Project</title>
</head>
<body>
	<p>Hello, <?php echo($victim); ?>!</p>
</body>
</html>