<?php
// Sets up a connection to the database>

$pdo = new PDO('mysql:host=localhost;dbname=ijdb;
charset=utf8', 'ijdbuser', 'ijdb2019%^&');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);