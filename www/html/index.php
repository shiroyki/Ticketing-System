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

// if (!$_SESSION['auth']){
// 	header('Location: login.php');
// 	exit();
// }

?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Ticketing System - Home</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="style.css">
</head>

<body>
   
  <!-- Navigation bar -->
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
    <h2>All Tickets</h2>
      <div class="form-group">
        <label for="status">Filter by Status:</label>
        <select class="form-control" id="status" name="status" onchange="filtering()">
          <option value="">All</option>
          <option value="new">New</option>
          
          <option value="open">Open</option>
          <option value="answered">Answered</option>
          <option value="postponed">Postponed</option>
          <option value="resolved">Resolved</option>
          
        </select>
      <!-- </div><button type="submit" class="btn btn-primary">Apply Filter</button> -->

    <table class="table table-striped">
      <thead>
        <tr>
          <th>Ticket ID</th>
          <th>Subject</th>
          <th>Status</th>
         
        </tr>
      </thead>
      <tbody id="ticket-table-body">
        <!-- Ticket rows will be added dynamically with JavaScript -->
        <?php //echo $_POST["status"];
?>
      </tbody>
    </table>
  </div>



  <!-- jQuery and Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
 
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <!-- Custom JavaScript -->
  <script src="index.js"></script>
</body>
</html>
