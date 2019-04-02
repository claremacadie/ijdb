<?php if (!isset($_SESSION)) session_start();?>
<!DOCTYPE html>

<html lang="en">

<head>

  <title>Clare's Database project</title>

</head>

<body>

  <?php
    // set logged in variables
    if (isset($_SESSION['displayName'])) $displayName = $_SESSION['displayName'];
    if (isset($_SESSION['firstName'])) $firstName = $_SESSION['firstName'];
    if (isset($_SESSION['lastName'])) $lastName = $_SESSION['lastName'];
  ?>

  <div class="container">

    <div class="page-content">
    <?php
      // Load the specific page's content
      if (isset($content))
        include $content;
    ?>
    </div>

  </div> <!-- /container -->

  <!-- Javascript
   ================================================== -->
  <!-- Placed at the end of the document so the pages load faster -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
  <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>

  <?php
    // Load the specific page's additional javascript content
    if (isset($contentjs))
      include $contentjs;
  ?>

</body>

</html>
