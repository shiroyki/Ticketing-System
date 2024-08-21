<?php
require __DIR__.'/lib/db.inc.php';
include_once('auth.php');
include_once('auth-process.php');

include_once('csrf-verify.php');

session_start();
if(!empty($_SESSION["auth"]))
{
    $user = auth();
       
}
else
{
    $user = 'Guest';
}
//if (!$_SESSION['auth']){
	//header('Location: login.php');
	//exit();
//}
date_default_timezone_set('Asia/Hong Kong');
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Ticket Details</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"><link rel="stylesheet" href="style.css">

</head>
<body>
 <nav class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-header">
        <a class="navbar-brand" href="index.php">Ticketing System</a>
      </div>
      <ul class="nav navbar-nav">
        <li><a href="index.php">Home</a></li>
        <li><a href="create_ticket.ph	p">Create Ticket</a></li>
        <li><a href="index.php">All Tickets</a></li>
        <li><a href="user_tickets.php">My Tickets</a></li>      </ul>
      <ul class="nav navbar-nav navbar-right">
       
<ul class="navbar-nav ml-auto">
  <?php if ($_SESSION["auth"] == false || ($user != 'Guest' && auth_admin()!= 1)) { ?>
    <li class="nav-item">
      <form class="form-inline my-1 my-lg-0 pr-3" id="logout" method="POST" action="admin-process.php?action=<?php echo($action ='logout'); ?>" enctype="multipart/form-data">
        <button class="nav-btn btn btn-outline-dark btn-light my-2 my-sm-0 btn-lg" type="submit" value="Submit">
          <?php if ($_SESSION["auth"] == false) {
            echo '<i class="bi bi-key-fill"></i><span class="glyphicon glyphicon-log-in"></span> Login';
          } elseif ($user != 'Guest' && auth_admin()!= 1) {
            echo '<span class="glyphicon glyphicon-log-out"></span> Logout';
          } ?>
        </button>
        <input type="hidden" name="nonce" value="<?php if( auth_admin()!= 1) echo csrf_getNonce($action); ?>"/>
      </form>
    </li>
  <?php } ?>

  <?php if($user != 'Guest' ) { ?>
    <li class="nav-item">
      <button class="nav-btn btn btn-outline-dark btn-light my-2 my-sm-0 btn-lg" type="button">
        <i class="bi bi-key-fill"></i> <a href="changePassword.php"><span class="glyphicon glyphicon-lock"></span> Change password</a>
      </button>
    </li>
  <?php } ?>

  <?php if ($user != 'Guest' && auth_admin()== 1) { ?>
    <li class="nav-item">
      <form class="d-inline my-2 my-lg-0 pr-3">
        <a class="btn btn-outline-dark btn-light my-2 my-sm-0 btn-lg" href="admin.php">
          <i class="bi bi-terminal"></i>
          <span class="glyphicon glyphicon-cog"></span>
          Admin Panel
        </a>
      </form>
    </li>
  <?php } ?>  <li class="nav-item ml-auto">
    <!-- add any additional menu items here --><h5><text class="my-2 my-sm-0">Welcome, <?php echo htmlspecialchars($user); ?> </text></h5>

  </li>
</ul>
    </div>

 
  </nav>

  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <?php
        // Initialize SQLite database connection
        session_start();
        $db = new PDO('sqlite:/var/www/tickets.db');

        // enable foreign key support
        $db->query('PRAGMA foreign_keys = ON;');

        // FETCH_ASSOC:
        // Specifies that the fetch method shall return each row as an
        // array indexed by column name as returned in the corresponding
        // result set. If the result set contains multiple columns with
        // the same name, PDO::FETCH_ASSOC returns only a single value
        // per column name.
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Check if the ticket ID is set in the URL
        if (isset($_GET['id'])) {
          // Get the ticket ID from the URL
          $id = $_GET['id'];
            
          // Build the query to retrieve the ticket details based on the ID
          $query = "SELECT * FROM tickets WHERE TID=:id";
          $stmt = $db->prepare($query);
          $stmt->bindParam(':id', $id, PDO::PARAM_INT);
          $stmt->execute();

          // Fetch the ticket details
          $ticket = $stmt->fetch();

          // Check if a ticket was found with the given ID
          if (!$ticket) {
            echo '<div class="alert alert-danger">Ticket not found</div>';
          } else {
            // Display the ticket details
            echo '<h2>Ticket Details</h2>';
            echo '<table class="table">';
            echo '<tr><th>Ticket ID</th><td>' . $ticket['TID'] . '</td></tr>';
            echo '<tr><th>Subject</th><td>' . $ticket['SUBJECT'] . '</td></tr>';
            echo '<tr><th>Description</th><td>' . $ticket['DESCRIPTION'] . '</td></tr>';
            echo '<tr><th>Status</th><td>' . $ticket['STATUS'] . '</td></tr>';
            echo '<tr><th>Created By</th><td>' . $ticket['USERID'] . '</td></tr>';
            echo '<tr><th>Created At</th><td>' . $ticket['CREATED_AT'] . '</td></tr>';
            echo '<tr><th>Last Updated At</th><td>' . $ticket['LAST_UPDATED_AT'] . '</td></tr>';
            echo '</table>';

            // Display the comments section
            echo '<h3>Comments</h3><form method="post">
  <div class="form-group">
    <label for="comment">Add a comment:</label>
    <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>';

            if (isset($_POST['comment'])) {
  // Get the comment from the form data
  $comment = htmlspecialchars($_POST['comment']);
  
  // Get the user id (you need to replace this with your own code to retrieve the user id)
  //$userId = 1;
$userId = $_SESSION["userid"];
  // Get the ticket id
  $ticketId = $_GET['ID'];
   date_default_timezone_set('Asia/Hong Kong');

  // Build the query to insert the comment into the database
  $query = "INSERT INTO COMMENTS (USERID, TID, TEXT, LAST_UPDATED_AT) VALUES (:userId, :ticketId, :text, :date)";
  $stmt = $db->prepare($query);
  $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
  $stmt->bindParam(':ticketId', $id, PDO::PARAM_INT);
  $stmt->bindParam(':text', $comment, PDO::PARAM_STR);
  $stmt->bindParam(':date', date('Y-m-d H:i:s'), PDO::PARAM_STR);

  //$stmt->bindParam(':image', $image, PDO::PARAM_STR); // Replace $image with the path to the image file if you want to include an image with the comment
  $stmt->execute();
}

// Build the query to retrieve all comments for this ticket
$query = "SELECT * FROM COMMENTS WHERE TID = :ticketId ORDER BY CID DESC";
$stmt = $db->prepare($query);
$stmt->bindParam(':ticketId', $_GET['id'], PDO::PARAM_INT);
$stmt->execute();
$comments = $stmt->fetchAll();

// Display the comments
echo '<div class="panel panel-default">';
echo '<div class="panel-body">';
if (count($comments) > 0) {
  foreach ($comments as $comment) {
    echo '<div class="media">';
    echo '<div class="media-left">';
    echo '<a href="#">';
   // echo '<img class="media-object" src="https://placehold.it/64x64" alt="User Avatar">';
    echo '<span class="glyphicon glyphicon-user"></span>';
    echo '</a>';
    echo '</div>';
    echo '<div class="media-body">';
    echo '<h4 class="media-heading"> User ' . $comment['USERID'] . ' <small><i>' . $comment['LAST_UPDATED_AT'] . '</i></small></h4>';
    echo '<p>' . $comment['TEXT'] . '</p>';
    echo '</div>';
    echo '</div>';
  }
} else {
  echo '<p>No comments yet.</p>';
}
echo '</div>';
echo '</div>';

// Display the latest comment form
echo '<form method="post">';
echo '<div class="form-group">';
echo '<label for="comment">Add a comment:</label>';
echo '<textarea class="form-control" id="comment" name="comment" rows="3"></textarea>';
echo '</div>';
echo '<button type="submit" class="btn btn-primary">Submit</button>';
echo '</form>';         }
        } else {
          echo '<div class="alert alert-danger">Ticket ID not provided</div>';
        }
        ?>
      </div>
    </div>
  </div>

  <!-- jQuery and Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>