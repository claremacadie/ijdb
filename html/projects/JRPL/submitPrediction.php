<?php

// Start the session if needed
if (!isset($_SESSION)) session_start();

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// Includes
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/includesfile.inc.php';

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// Check to see if user is logged in
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if (!userIsLoggedIn() || !isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] == FALSE) {
  // build results into output JSON file
  header('Content-type: application/json');
  $arr = array(
    'result' => 'No'
    ,'message' => 'You are not logged in. Please log in first');
  echo json_encode($arr);
  die();
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// Check to see if we've got the right action posted
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
if (isset($_POST['action']) && $_POST['action'] == 'submitPrediction')
{

  // Assign values
  $userID = $_SESSION['userID'];
  $matchID = (isset($_POST['matchID'])) ? $_POST['matchID'] : 'null';
  $homeTeamScore = (isset($_POST['homeTeamScore'])) ? $_POST['homeTeamScore'] : 'null';
  $awayTeamScore = (isset($_POST['awayTeamScore'])) ? $_POST['awayTeamScore'] : 'null';

  // Get DB connection
  include $_SERVER['DOCUMENT_ROOT'] . '/includes/db.inc.php';
  if (!isset($link)) {
    $error = 'Error getting DB connection';

    header('Content-type: application/json');
    $arr = array('result' => 'No', 'message' => $error);
    echo json_encode($arr);
    die();
  }

  // Make sure submitted data is clean
  $userID = mysqli_real_escape_string($link, $userID);
  $matchID = mysqli_real_escape_string($link, $matchID);
  $homeTeamScore = mysqli_real_escape_string($link, $homeTeamScore);
  $awayTeamScore = mysqli_real_escape_string($link, $awayTeamScore);

  // Check if match is locked down first
  $sql = "
    SELECT
      CASE
        WHEN DATE_ADD(NOW(), INTERVAL 30 MINUTE) > TIMESTAMP(m.`Date`, m.`KickOff`) THEN 1
        ELSE 0
      END AS `Closed`
    FROM `Match` m
    WHERE m.`MatchID` = " . $matchID . ";";

  // Run query and handle any failure
  $result = mysqli_query($link, $sql);
  if (!$result)
  {
    $error = 'Error finding current lock-down status: <br />' . mysqli_error($link) . '<br /><br />' . $sql;

    header('Content-type: application/json');
    $arr = array('result' => 'No', 'message' => $error);
    echo json_encode($arr);
    die();
  }

  $row = mysqli_fetch_assoc($result);
  if ($row['Closed'] == 1) {
    // build results into output JSON file
    header('Content-type: application/json');
    $arr = array(
      'result' => 'No'
      ,'message' => 'Match is already locked-down');
    echo json_encode($arr);
    die();
  }

  if (!int($matchID) || ($matchID < 1)) {
    // build results into output JSON file
    header('Content-type: application/json');
    $arr = array(
      'result' => 'No'
      ,'message' => 'Submitted match number "' . $matchID . '" is not correctly formatted. It should be an integer greater than 1');
    echo json_encode($arr);
    die();
  }

  if (!int($homeTeamScore) || !int($awayTeamScore)) {
    // build results into output JSON file
    header('Content-type: application/json');
    $arr = array(
      'result' => 'No'
      ,'message' => 'Submitted predictions are not integers');
    echo json_encode($arr);
    die();
  }

  if (($homeTeamScore < 0) || ($awayTeamScore < 0)) {
    // build results into output JSON file
    header('Content-type: application/json');
    $arr = array(
      'result' => 'No'
      ,'message' => 'Submitted predictions are negative');
    echo json_encode($arr);
    die();
  }

  // Query to see if a prediction for this match already exists
  $sql = "SELECT COUNT(*) AS `Count`
      FROM `Prediction` p
      WHERE
        p.`MatchID` = " . $matchID . "
        AND p.`UserID` =  " . $userID . ";";

  // Run query and handle any failure
  $result = mysqli_query($link, $sql);
  if (!$result)
  {
    $error = 'Error counting existing predictions: <br />' . mysqli_error($link) . '<br /><br />' . $sql;

    header('Content-type: application/json');
    $arr = array('result' => 'No', 'message' => $error);
    echo json_encode($arr);
    die();
  }

  // Check result to see if we need to UPDATE or INSERT
  $row = mysqli_fetch_assoc($result);
    if ($row['Count'] == 1) {
    // Entry already exists so UPDATE
    $sql = "UPDATE `Prediction`
        SET `HomeTeamPoints` = " . $homeTeamScore . ",
          `AwayTeamPoints` = " . $awayTeamScore . ",
          `DateAdded` = NOW()
        WHERE
          `MatchID` = " . $matchID . "
          AND `UserID` =  " . $userID . ";";

    // Run query and handle any failure
    $result = mysqli_query($link, $sql);
    if (!$result)
    {
      $error = 'Error updating prediction: <br />' . mysqli_error($link) . '<br /><br />' . $sql;

      header('Content-type: application/json');
      $arr = array('result' => 'No', 'message' => $error);
      echo json_encode($arr);
      die();
    }
  } else {
    // No entry already exists so INSERT
    $sql = "INSERT INTO `Prediction`
          (`UserID`,
          `MatchID`,
          `HomeTeamPoints`,
          `AwayTeamPoints`,
          `DateAdded`)
        VALUES
          (" . $userID . ",
          " . $matchID . ",
          " . $homeTeamScore . ",
          " . $awayTeamScore . ",
          NOW());";

    // Run query and handle any failure
    $result = mysqli_query($link, $sql);
    if (!$result)
    {
      $error = 'Error inserting prediction: <br />' . mysqli_error($link) . '<br /><br />' . $sql;

      header('Content-type: application/json');
      $arr = array('result' => 'No', 'message' => $error);
      echo json_encode($arr);
      die();
    }
  };

  // Test Code
  /*header('Content-type: application/json');
  $arr = array('result' => 'No', 'message' => $sql);
  echo json_encode($arr)*/;

  // build results into output JSON file
  header('Content-type: application/json');
  $arr = array(
    'result' => 'Yes'
    ,'message' => '');
  echo json_encode($arr);

}
