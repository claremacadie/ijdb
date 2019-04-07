<?php 
//This function loads classes if they have not already been previously loaded
//spl_autolad_register is a built in PHP function
//It tells PHP to call the function with the name that's given if it comes across a class that hasn't yet been included
function autoloader($className)
{
	$file = __DIR__ . '/../classes/' . $className . '.php';
	include $file;
}

spl_autoload_register('autoloader');