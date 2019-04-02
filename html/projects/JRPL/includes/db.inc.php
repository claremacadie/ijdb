<?php

if (getenv('ENVIR') == 'Production') {
  $user = 'jrpl_prod_user';
  $pwd = getenv('DB_PWD');
  $db = 'jrpl_prod';
} elseif (getenv('ENVIR') == 'Test') {
  $user = 'jrpl_test_user';
  $pwd = getenv('DB_PWD');
  $db = 'jrpl_test';
} elseif (getenv('ENVIR') == 'Development') {
  $user = 'jrpl_dev_user';
  $pwd = getenv('DB_PWD');
  $db = 'jrpl_dev';
}

$link = mysqli_connect('localhost', $user, $pwd);
if (!$link)
{
  $error = 'Unable to connect to the database server';
  include 'error/index.php';
  exit();
}

if (!mysqli_set_charset($link, 'utf8'))
{
  $error = 'Unable to set database connection encoding';
  include 'error/index.php';
  exit();
}

if (!mysqli_select_db($link, $db))
{
  $error = 'Unable to locate the database';
  include 'error/index.php';
  exit();
}
