<?php
	// You can "call" other PHP files using include (or require)
	// The PHP pre-processor will read these lines and stitch together everything
	// into one big file
	include '3a.php';
?>
<html>
<head>
	<title>Clare's Database Project</title>
</head>
<body>
	<!-- $victim is now not defined in this document but PHP finds it in 3a.php, 
	     which got included in above. The line below still works -->
	<p>Hello, <?php echo($victim); ?>!</p>
</body>
</html>