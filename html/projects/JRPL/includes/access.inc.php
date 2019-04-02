<?php

// Function to process logging in
function userIsLoggedIn() {

  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
  // Log In
  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

  // Check request has been properly posted
  if (isset($_POST['action']) && $_POST['action'] === 'login') {

    // Check the email has been posted & filled in
    if (!isset($_POST['email']) || $_POST['email'] == '') {
      $GLOBALS['loginError'] = 'Please fill in e-mail address field';
      return false;
    }

    // Hash the posted password
    $password = md5($_POST['password'] . 'jrp');

    // Validate the email & (hashed) password combination
    if (correctPassword($_POST['email'], $password)){

      // Correct log in details provided
      // Load the user info into the session
      setUserSessionInfo($_POST['email']);

      // Set remember me cookie, if asked for
      if (isset($_POST['rememberMe']) && $_POST['rememberMe'] === 'true') {
        setRMCookie($_SESSION['userID']);
      }

      // return success
      return true;

    } else {

      // Wipe the session information
      unsetUserSessionInfo();

      // Return error based on type of failure
      if (databaseContainsUser($_POST['email'])) {
        $GLOBALS['loginError'] = 'The password was incorrect';
        return false;
      } else {
        $GLOBALS['loginError'] = 'The specified email address does not exist';
        return false;
      }
    }
  }

  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
  // Next check session for logged in status
  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
  if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']) {
  // Check by re-validating the e-mail and (hashed) password
    return correctPassword($_SESSION['email'], $_SESSION['password']);
  }

  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
  // Finally check remember me cookie
  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
  if (isset($_COOKIE['rmUserID']) && isset($_COOKIE['rmSeriesID']) && isset($_COOKIE['rmToken'])) {

    return  checkRMCookie();

  } else {
    // if everything fails then the user is no logged in
    return false;
  }
}

// Function to check remember me cookie
function checkRMCookie() {

  // Check cookie data is properly set
  if (isset($_COOKIE['rmUserID']) && isset($_COOKIE['rmSeriesID']) && isset($_COOKIE['rmToken'])) {

    // Get link to database
    include 'db.inc.php';
    if (!isset($link)) {
      $error = 'Error getting DB connection';

      header('Content-type: application/json');
      $arr = array('result' => 'No', 'message' => $error);
      echo json_encode($arr);
      die();
    }

    // Make data safe
    $userID = mysqli_real_escape_string($link, $_COOKIE['rmUserID']);
    $seriesID = mysqli_real_escape_string($link, $_COOKIE['rmSeriesID']);
    $token = mysqli_real_escape_string($link, $_COOKIE['rmToken']);

    // Hash the token
    $tokenh = md5($token . 'jrp');

    // Test if cookie is ok
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    // Write the SQL
    $sql = "SELECT COUNT(*)
        FROM `RememberMe`
        WHERE `UserID` = " . $userID . "
          AND `SeriesID` = " . $seriesID . "
          AND `Token` = '" . $tokenh . "';";

    // Run the SQL and process any error
    $result = mysqli_query($link, $sql);
    if (!$result) {
      $error = 'Error finding count of existing cookies: <br />' . mysqli_error($link) . '<br /><br />' . $sql;

      header('Content-type: application/json');
      $arr = array('result' => 'No', 'message' => $error);
      echo json_encode($arr);
      die();
    }

    // Check results of query and act accordingly
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    $row = mysqli_fetch_row($result);
    if ($row[0] > 0) {
    // Cookie matches

      // Create a new token
      $token = round(mt_rand(0, 1000000000));

      // Hash the token to store in the DB
      $tokenh = md5($token . 'jrp');

      //   Update token on DB
      // ~~~~~~~~~~~~~~~~~~~~~~~~~

      // Write the SQL
      $sql = "UPDATE `RememberMe`
          SET `Token` = '" . $tokenh . "',
            `DateAdded` = NOW()
          WHERE `UserID` = " . $userID . "
            AND `SeriesID` = " . $seriesID . ";";

      // Run the SQL and process any error
      $result = mysqli_query($link, $sql);
      if (!$result) {
        $error = 'Error updating token for cookie: <br />' . mysqli_error($link) . '<br /><br />' . $sql;

        header('Content-type: application/json');
        $arr = array('result' => 'No', 'message' => $error);
        echo json_encode($arr);
        die();
      }

      //   Re-issue cookies with new token
      setcookie('rmUserID', $userID, time() + (30 * 86400), '/'); // 30 days
      setcookie('rmSeriesID', $seriesID, time() + (30 * 86400), '/'); // 30 days
      setcookie('rmToken', $token, time() + (30 * 86400), '/'); // 30 days

      // Load the user info into the session
      setUserSessionInfo('', $userID);

      // Set return value for function to true
      $out = true;

    } else {
    // Cookie does not match

      // Check if userID & seriesID combo exists (i.e. just token was wrong)

      // Write the SQL
      $sql = "SELECT COUNT(*)
          FROM `RememberMe`
          WHERE `UserID` = " . $userID . "
            AND `SeriesID` = " . $seriesID . ";";

      // Run the SQL and process any error
      $result = mysqli_query($link, $sql);
      if (!$result) {
        $error = 'Error finding count of existing cookies with non-matching tokens: <br />' . mysqli_error($link) . '<br /><br />' . $sql;

        header('Content-type: application/json');
        $arr = array('result' => 'No', 'message' => $error);
        echo json_encode($arr);
        die();
      }

      if ($row[0] > 0) {

        // Delete all user's cookies from DB
        // ~~~~~~~~~~~~~~~~~~~~~~~~~

        // Write the SQL
        $sql = "DELETE FROM `RememberMe`
            WHERE `UserID` = " . $userID . ";";

        // Run the SQL and process any error
        $result = mysqli_query($link, $sql);
        if (!$result) {
          $error = 'Error deleting existing cookies: <br />' . mysqli_error($link) . '<br /><br />' . $sql;

          header('Content-type: application/json');
          $arr = array('result' => 'No', 'message' => $error);
          echo json_encode($arr);
          die();
        }

        //TODO: should write back a warning about suspected cookie theft
      }

      // Unset session info
      unsetUserSessionInfo();

      // Set return value for function to false
      $out = false;
    }

    // Close connection
    mysqli_close($link);

    // Pass back the result
    return $out;

  } else {
    return false;
  }

}

// Function to set remember me cookies
function setRMCookie($userID) {

  // Get link to database
  include 'db.inc.php';
  if (!isset($link)) {
    $error = 'Error getting DB connection';

    header('Content-type: application/json');
    $arr = array('result' => 'No', 'message' => $error);
    echo json_encode($arr);
    die();
  }

  // Make data safe
  $userID = mysqli_real_escape_string($link, $userID);

  // Create new seriesID & token
  $seriesID = round(mt_rand(0, 1000000000));
  $token = round(mt_rand(0, 1000000000));

  // Hash the token to store in the DB
  $tokenh = md5($token . 'jrp');

  // Write UserID, seriesID & token to DB
  // ~~~~~~~~~~~~~~~~~~~~~~~~~

  // Write the SQL
  $sql = "INSERT INTO `RememberMe`
        (`UserID`,`SeriesID`,`Token`,`DateAdded`)
      VALUES
        (" . $userID . ", " . $seriesID . ", '" . $tokenh . "', NOW());";

  // Run the SQL and process any error
  $result = mysqli_query($link, $sql);
  if (!$result) {
    $error = 'Error adding new cookie details: <br />' . mysqli_error($link) . '<br /><br />' . $sql;

    header('Content-type: application/json');
    $arr = array('result' => 'No', 'message' => $error);
    echo json_encode($arr);
    die();
  } else {
    // Set cookies with same values
    setcookie('rmUserID', $userID, time() + (30 * 86400), '/'); // 30 days
    setcookie('rmSeriesID', $seriesID, time() + (30 * 86400), '/'); // 30 days
    setcookie('rmToken', $token, time() + (30 * 86400), '/'); // 30 days
    $out = true;
  }

  // Close connection
    mysqli_close($link);

  // Pass back the result
  return $out;

}

// Function to process updating details
function updateDetails() {

  // Check request has been properly posted
  if (isset($_POST['action']) and $_POST['action'] === 'update') {

  // Grab posted details
  if (isset($_POST['displayName'])) $displayName = $_POST['displayName'];
  if (isset($_POST['firstName'])) $firstName = $_POST['firstName'];
  if (isset($_POST['lastName'])) $lastName = $_POST['lastName'];
  if (isset($_POST['email'])) $email = $_POST['email'];
  if (isset($_POST['pwd'])) $password = $_POST['pwd'];
  if (isset($_POST['pwd2'])) $password2 = $_POST['pwd2'];

  // Start session and grab UserID from it
  if (!isset($_SESSION)) session_start();
  if (isset($_SESSION['userID'])) $userID = $_SESSION['userID'];

  // If the two passwords don't match then throw an error
  if ($password <> $password2) {
    $GLOBALS['loginError'] = 'The passwords do not match';
    return false;
  }

  // Set flag for whether we're updating passwords or not
  // allows form to be posted with blanks for passwords and not hev them updated
  $updatePasswords = ($password != '');

  // Hash the password
  $password = md5($password . 'jrp');

  // Open a link to the database
  include 'db.inc.php';
  if (!isset($link)) {
    $error = 'Error getting DB connection';

    header('Content-type: application/json');
    $arr = array('result' => 'No', 'message' => $error);
    echo json_encode($arr);
    die();
  }

  // Make data safe
  $displayName = mysqli_real_escape_string($link, $displayName);
  $firstName = mysqli_real_escape_string($link, $firstName);
  $lastName = mysqli_real_escape_string($link, $lastName);
  $email = mysqli_real_escape_string($link, $email);
  $password = mysqli_real_escape_string($link, $password);
  $userID = mysqli_real_escape_string($link, $userID);

  // Write the SQL command depending on whether we're updating the password or not
  if ($updatePasswords) {
    $sql = "UPDATE `User`
            SET `DisplayName` = '" . $displayName . "',
                `FirstName` = '" . $firstName . "',
                `LastName` = '" . $lastName . "',
                `Email` = '" . $email . "',
                `Password` = '" . $password . "'
            WHERE `UserID` = " . $userID . " ";
  } else {
    $sql = "UPDATE `User`
            SET `DisplayName` = '" . $displayName . "',
                `FirstName` = '" . $firstName . "',
                `LastName` = '" . $lastName . "',
                `Email` = '" . $email . "'
            WHERE `UserID` = " . $userID . ";";
  }

  // Run the query and pass back the error on failure
  $result = mysqli_query($link, $sql);
  if (!$result) {
    $GLOBALS['loginError'] = 'Error updating details: <br />' . mysqli_error($link) . '<br /><br />' . $sql;
    return false;
  }

  // Load the user info into the session
  setUserSessionInfo($email);

  return true;

  }

}

// Function to check email and (hashed) password combination exists in the database
function correctPassword($email, $password) {

  // Get link to database
  include 'db.inc.php';
  if (!isset($link)) {
    $error = 'Error getting DB connection';

    header('Content-type: application/json');
    $arr = array('result' => 'No', 'message' => $error);
    echo json_encode($arr);
    die();
  }

  // Make data safe
  $email = mysqli_real_escape_string($link, $email);
  $password = mysqli_real_escape_string($link, $password);

  // Write the SQL
  $sql = "SELECT COUNT(*) FROM `User`
          WHERE `Email` = '" . $email . "'
            AND `Password` = '" . $password . "';";

  // Run the SQL and process any error
  $result = mysqli_query($link, $sql);
  if (!$result) {
    $error = 'Error searching for user password combination: <br />' . mysqli_error($link) . '<br /><br />' . $sql;

    header('Content-type: application/json');
    $arr = array('result' => 'No', 'message' => $error);
    echo json_encode($arr);
    die();
  }

  // Read the result
  $row = mysqli_fetch_row($result);
  $out = ($row[0] > 0);

  // Close connection
  mysqli_close($link);

  // Pass back the result
  return $out;
}

// Function to determine if the user is in the database
function databaseContainsUser($email) {

  // Get link to database
  include 'db.inc.php';
  if (!isset($link)) {
    $error = 'Error getting DB connection';

    header('Content-type: application/json');
    $arr = array('result' => 'No', 'message' => $error);
    echo json_encode($arr);
    die();
  }

  // Make data safe
  $email = mysqli_real_escape_string($link, $email);

  // Write the SQL
  $sql = "SELECT COUNT(*) FROM `User`
            WHERE `Email`='" . $email . "';";

  // Run the SQL and process any error
  $result = mysqli_query($link, $sql);
  if (!$result) {
    $error = 'Error searching for user: <br />' . mysqli_error($link) . '<br /><br />' . $sql;

    header('Content-type: application/json');
    $arr = array('result' => 'No', 'message' => $error);
    echo json_encode($arr);
    die();
  }

  // Read the result
  $row = mysqli_fetch_row($result);
  $out = ($row[0] > 0);

  // Close connection
  mysqli_close($link);

  // Pass back the result
  return $out;
}

// Function to determine if the current user has a particular role
function userHasRole($role) {

  // Get link to database
  include 'db.inc.php';
  if (!isset($link)) {
    $error = 'Error getting DB connection';

    header('Content-type: application/json');
    $arr = array('result' => 'No', 'message' => $error);
    echo json_encode($arr);
    die();
  }

  // Make data safe
  $userID = mysqli_real_escape_string($link, $_SESSION['userID']);
  $role = mysqli_real_escape_string($link, $role);

  // Write the SQL
  $sql = "SELECT COUNT(*)
      FROM `User` u
        INNER JOIN `UserRole` ur ON ur.UserID = u.`UserID`
        INNER JOIN `Role` r ON r.RoleID = ur.`RoleID`
            WHERE u.`UserID` = " . $userID . "
        AND r.`Name` = '" . $role. "';";

  // Run the SQL and process any error
  $result = mysqli_query($link, $sql);
  if (!$result) {
    $error = 'Error searching for user roles: <br />' . mysqli_error($link) . '<br /><br />' . $sql;

    header('Content-type: application/json');
    $arr = array('result' => 'No', 'message' => $error);
    echo json_encode($arr);
    die();
  }

  // Read the result
  $row = mysqli_fetch_row($result);
  $out = ($row[0] > 0);

  // Close connection
  mysqli_close($link);

  // Pass back the result
  return $out;
}

// Function to set session variables based on user info that is in the database
function setUserSessionInfo($email = '', $id = 0) {

  // Get link to database
  include 'db.inc.php';
  if (!isset($link)) {
    $error = 'Error getting DB connection';

    header('Content-type: application/json');
    $arr = array('result' => 'No', 'message' => $error);
    echo json_encode($arr);
    die();
  }

  // Make data safe
  $email = mysqli_real_escape_string($link, $email);
  $id = mysqli_real_escape_string($link, $id);

  // Get all fields except role
  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

  if ($email == '') {
  // Write the SQL
  $sql = "SELECT `UserID`,`Email`,`Password`,`FirstName`,`LastName`,`DisplayName`
        FROM `User` u
        WHERE u.`UserID` = " . $id . "
        LIMIT 1;";
  } else {
  $sql = "SELECT `UserID`,`Email`,`Password`,`FirstName`,`LastName`,`DisplayName`
        FROM `User` u
        WHERE u.`Email` = '" . $email . "'
        LIMIT 1;";

  }

  // Run the SQL and process any error
  $result = mysqli_query($link, $sql);
  if (!$result) {
    $error = 'Error selecting user details: <br />' . mysqli_error($link) . '<br /><br />' . $sql;

    header('Content-type: application/json');
    $arr = array('result' => 'No', 'message' => $error);
    echo json_encode($arr);
    die();
  }
  $row = mysqli_fetch_assoc($result);

  // Set session variables
  if (!isset($_SESSION)) session_start();
  $_SESSION['loggedIn'] = true;
  $_SESSION['email'] = $row['Email'];
  $_SESSION['userID'] = $row['UserID'];
  $_SESSION['password'] = $row['Password'];
  $_SESSION['firstName'] = $row['FirstName'];
  $_SESSION['lastName'] = $row['LastName'];
  $_SESSION['displayName'] = $row['DisplayName'];

  // Get admin role
  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

  // Write the SQL
  $sql = "SELECT COUNT(*)
      FROM `User` u
        INNER JOIN `UserRole` ur ON ur.`UserID` = u.`UserID`
        INNER JOIN `Role` r ON r.`RoleID` = ur.`RoleID`
            WHERE u.`UserID` = " . $_SESSION['userID'] . " AND r.`Role`='Admin'";

  // Run the SQL and process any error
  $result = mysqli_query($link, $sql);
  if (!$result) {
    $error = 'Error selecting user roles: <br />' . mysqli_error($link) . '<br /><br />' . $sql;

    header('Content-type: application/json');
    $arr = array('result' => 'No', 'message' => $error);
    echo json_encode($arr);
    die();
  }

  // Set the session variable based on the result
  $row = mysqli_fetch_row($result);
  $_SESSION['isAdmin'] = ($row[0] > 0);

  // Close connection
  mysqli_close($link);

}

// Unset all the session info when a user logs off or has wrong
// log in credentials
function unsetUserSessionInfo() {

  if (!isset($_SESSION)) session_start();
  //unset($_SESSION['loggedIn']);
  $_SESSION['loggedIn'] = FALSE;
  unset($_SESSION['userID']);
  unset($_SESSION['email']);
  unset($_SESSION['password']);
  unset($_SESSION['firstName']);
  unset($_SESSION['lastName']);
  unset($_SESSION['displayName']);
  unset($_SESSION['isAdmin']);

  // Delete the cookies
  setcookie('rmUserID', '', time() - 3600, '/');
  setcookie('rmSeriesID', '', time() - 3600, '/');
  setcookie('rmToken', '', time() - 3600, '/');
}
