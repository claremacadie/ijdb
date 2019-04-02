<?php
	/* The <?php opening and ?> closing tags donate PHP code that
	   is to be pre-processed by PHP on the server before the document
	   is sent out on the internet
	   
	   N.B. This is how you wirte comments in PHP */
	// You can also use double slashes to comment line by line
	
	// All varaibles in PHP start with a $
	// = means assign value, if you want to check whther two things are equal you'd use ==
	// All PHP lines end with a semi-colon. Unlike SQL you have to do this or you'll get an error
	$victim="World";
	
	/* So this whole block of PHP has created a variable called victim; we will use it later*/
?>
<html>
<head>
	<title>Clare's Database Project</title>
</head>
<body>
	<!-- Another bit of PHP written in-line amongst the HTML.
	     HTML comments here as we're in the HTML bit, not PHP.
	     The echo function writes it's paramter, $victim, to the output webpage that gets served -->
	<p>Hello, <?php echo($victim); ?>!</p>
	<!-- If you inspect the source of this file from your browser (F12 in Chrome) you'll 
	     see these HTML comments but you won't see any of the PHP bits as they got processed
		 by the server before this document got sent. -->
	<!-- Above it should just say <p>Hello, World!</p> and as a receiver of the webpage
	     you will be none the wiser that PHP processing has occurred. -->
</body>
</html>