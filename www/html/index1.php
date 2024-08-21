

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
        <a class="navbar-brand" href="#">Ticketing System</a>
      </div>
      <ul class="nav navbar-nav">
        <li><a href="#">Home</a></li>
        <li><a href="create_ticket.php">Create Ticket</a></li>
        <li><a href="?status=active">Active Tickets</a></li>
        <li><a href="?status=unsolved">Unsolved Tickets</a></li>
        <li><a href="?status=closed">Closed Tickets</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
     
      </ul>
    </div>
  </nav>

  <div class="container">
    
      <h2>My Tickets</h2>
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Ticket ID</th>
            <th>Subject</th>
            <th>Status</th>
            <th>Number of Comments</th>
          </tr>
        </thead>
        <tbody>
          
        </tbody>
      </table>
    
      <h2>Welcome to the Ticketing System</h2>
      <p>Please login to view your tickets or create a new ticket.</p>
    
  </div>

  <!-- Login modal -->
  <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="loginModalLabel">Login</h4>
        </div>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
          <div class="modal-body">
            <?php if (isset($error_message)) { ?>
              <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php } ?>
            <div class="form-group">
              <label for="username">Username</label>
              <input type="text" class="form-control" name="username" required>
            </div>
            <div class="form-group">
              <label for="password">Password</label>
              <input type="password" class="form-control" name="password" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" name="login" class="btn btn-primary">Login</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- jQuery and Bootstrap JS -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>

</html>