<?php
// Make sure all relevant includes are loaded

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// Set-up
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

// Make sure all relevant includes are loaded
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/includesfile.inc.php';

// Check $_GET and $_SESSION variables for Match ID
if (isset($_GET['id'])) {
  $gMatchID = $_GET['id'];
  $_SESSION['matchID'] = $gMatchID;
} elseif (isset($_SESSION['matchID'])) {
  $gMatchID = $_SESSION['matchID'];
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// Process Match ID
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

//Check if matchID has been posted
if (isset($gMatchID) && int($gMatchID) && ($gMatchID > 0)) {
  // MatchID has been properly posted so proceed

  // Call the getMatchDetails script to load all the variables for the match page
  include 'getMatchDetails.php';
  
  $message = 'Clare';

  // Set content pointers
  $content = $_SERVER['DOCUMENT_ROOT'] . '/match.html.php';

} else {
  // MatchID has not been properly posted so return error

  // Set content pointer to bad match page
  $content = $_SERVER['DOCUMENT_ROOT'] . '/badMatch.html.php';

}

// Call main HTML page
$contentjs = $_SERVER['DOCUMENT_ROOT'] . '/match.js.php';
include $_SERVER['DOCUMENT_ROOT'] . '/includes/template.html.php';

?>