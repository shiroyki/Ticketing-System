<?php
require __DIR__.'/lib/db.inc.php';
include_once('auth.php');

include_once('csrf-verify.php');

session_start();


if(auth() == false || auth_admin() != 1) {
    header('Location: login.php');
    exit();
} 




// Start session to access user information
session_start();

// Check if user is an administrator
//if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
  // Redirect to login page if user is not an administrator
  //header('Location: login.php');
 // exit();
//}
if (isset($_POST['nonce']) && isset($_GET['action']) && $_GET['action'] === 'logout') {
  // Validate nonce to prevent CSRF attacks
  if (!csrf_verifyNonce($_GET['action'], $_POST['nonce'])) {
    die('Invalid CSRF token');
  }

  // Destroy session and redirect to login page
  session_destroy();
  header('Location: login.php');
  exit();
}




// Initialize SQLite database connection
$db = new PDO('sqlite:/var/www/tickets.db');

// Enable foreign key support
$db->query('PRAGMA foreign_keys = ON;');

// FETCH_ASSOC:
// Specifies that the fetch method shall return each row as an
// array indexed by column name as returned in the corresponding
// result set. If the result set contains multiple columns with
// the same name, PDO::FETCH_ASSOC returns only a single value
// per column name.
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

// Check if a ticket ID is set in the URL for modifying the ticket status or deleting the ticket
if (isset($_GET['id'])) {
  $ticket_id = $_GET['id'];
  
  // Check if the action parameter is set for modifying or deleting the ticket
  if (isset($_GET['action'])) {
    $action = $_GET['action'];

if (isset($_POST['status'])) {
   // echo "Status: " . $_POST['status'];
   
    $ticket_id = $_GET['id'];
   // echo " tid: " . $ticket_id;
 //echo "action: " . $action;

     if ($action === 'status'){
//echo "Status action: " . $_POST['status'];
$status = $_POST['status'];
//echo "Status action: " . $status;
   
      $ticket_id = $_GET['id'];
      //$time= datetime('now');
 //echo " tid action: " . $ticket_id;
      // Build the query to update the ticket status in the database
      $query = "UPDATE tickets SET STATUS=(?), LAST_UPDATED_AT=(?) WHERE TID=(?)";
      $stmt = $db->prepare($query);
      $stmt->bindParam(1, $status, PDO::PARAM_STR);
$stmt->bindParam(2, date('Y-m-d H:i:s'), PDO::PARAM_STR);
$stmt->bindParam(3, $ticket_id, PDO::PARAM_INT);      if($stmt->execute()){

      // Redirect back to the ticket details page
      header("Location: ticket_details.php?id=$ticket_id");
      exit();}


}

  }

    // Modify the ticket status if the action is "set_status"
  /*  if ($action === 'status' && isset($_POST['status'])) {
      $status = htmlspecialcharacter($_POST['status']);
      $ticket_id = $_GET['id'];
      // Build the query to update the ticket status in the database
      $query = "UPDATE tickets SET STATUS=(?), LAST_UPDATED_AT=datetime('now') WHERE TID=(?)";
      $stmt = $db->prepare($query);
      $stmt->bindParam(1, $status);
      $stmt->bindParam(2, $ticket_id);
      if($stmt->execute()){

      // Redirect back to the ticket details page
      header("Location: ticket_details.php?id=$ticket_id");
      exit();}
    }*/
    if ($action === 'comment') {
    // Get the ticket ID and comment text from the form submission
    $ticketID = $_GET['id'];
    $userID = $_SESSION['userid'];
    $commentText = $_POST['comment'];

    // Build the query to insert a new comment into the database
    $query = "INSERT INTO COMMENTS (USERID, TEXT, TID) VALUES (:userID, :commentText, :ticketID)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
    $stmt->bindParam(':commentText', $commentText, PDO::PARAM_STR);
    $stmt->bindParam(':ticketID', $ticketID, PDO::PARAM_INT);
    $stmt->execute();

    // Redirect back to the ticket details page
    //header("Location: admin.php?id=$ticketID");
    header("Location: ticket_details.php?id=$ticketID");
    exit();
}    // Delete the ticket if the action is "delete"
    if ($action === 'delete') {
      // Build the query to delete the ticket from the database
      $query = "DELETE FROM tickets WHERE TID=:ticket_id";
      $stmt = $db->prepare($query);
      $stmt->bindParam(':ticket_id', $ticket_id, PDO::PARAM_INT);
      $stmt->execute();

      // Redirect back to the ticket list page
      header('Location: admin.php');
      exit();
    }
if (isset($_GET['id']) && isset($_GET['action'])) {
    $user_id = $_GET['id'];
    $action = $_GET['action'];
if ($action === 'update_user' && isset($_POST['admin_flag'])) {
        $admin_flag = $_POST['admin_flag'];
        $user_id = $_GET['id'];

        // Build the query to update the user admin_flag in the database
        $query = "UPDATE ACCOUNT SET ADMIN_FLAG=:admin_flag WHERE USERID=:user_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':admin_flag', $admin_flag, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        // Redirect back to the admin panel
        header('Location: admin.php');
        exit();
    }
if ($action === 'delete_user') {
        // Build the query to delete the user from the database
        $query = "DELETE FROM ACCOUNT WHERE USERID=:user_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        // Redirect back to the admin panel
        header('Location: admin.php');
        exit();
    }
if ($action === 'update_email' && isset($_POST['email'])) {
        $email = $_POST['email'];
 $user_id = $_GET['id'];

        // Build the query to update the user email in the database
        $query = "UPDATE ACCOUNT SET EMAIL=:email WHERE USERID=:user_id";
        $stmt = $db->prepare($query); $stmt->bindParam(':email', $email, PDO::PARAM_STR);
 $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
echo $user_id;
         echo $email;
        // Redirect back to the admin panel
        header('Location: admin.php');
        exit();
    }
if ($action === 'delete_ticket') {
      // Build the query to delete the ticket from the database
$id = $_GET['id'];
      $query = "DELETE FROM tickets WHERE TID=:id";
      $stmt = $db->prepare($query);
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      $stmt->execute();

      // Redirect back to the ticket list page
      header('Location: admin.php');
      exit();
    }

// Modify the ticket status if the action is "set_status"
    if ($action === 'set_status' && isset($_POST['status'])) {
      $status = $_POST['status'];
      $id = $_GET['id'];

      // Build the query to update the ticket status in the database
      $query = "UPDATE tickets SET STATUS=:status, LAST_UPDATED_AT=datetime('now') WHERE TID=:id";
      $stmt = $db->prepare($query);
      $stmt->bindParam(':status', $status, PDO::PARAM_STR);
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      $stmt->execute();

      // Redirect back to the ticket details page
      header("Location: admin.php");
      exit();
    }
}
  }
}

// Build the query to retrieve all tickets in the database, ordered by creation date in descending order
$query = "SELECT * FROM tickets ORDER BY CREATED_AT DESC";
$stmt = $db->prepare($query);
$stmt->execute();

// Fetch all ticket data and store in an array
$tickets = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Panel</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>
<body>

<a  href="index.php">Return back to Main Page</a>
  
  <div class="container">

    <div class="row">
      <div class="col-md-12">
        <h2>Admin Panel</h2>
        <table class="table">
          <thead>
            <tr>
              <th>Ticket ID</th>
              <th>Subject</th>
              <th>Description</th>
              <th>Status</th>
              <th>Created At</th>
              <th>Last Updated At</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($tickets as $ticket): ?>
            <tr>
              <td><a href="ticket_details.php?id=<?php echo $ticket['TID']; ?>"><?php echo $ticket['TID']; ?></a></td>
              <td><?php echo $ticket['SUBJECT']; ?></td>
              <td><?php echo $ticket['DESCRIPTION']; ?></td>
              <td><?php echo $ticket['STATUS']; ?></td>
              <td><?php echo $ticket['CREATED_AT']; ?></td>
              <td><?php echo $ticket['LAST_UPDATED_AT']; ?></td>
              <td>
                <div class="btn-group">
                  <a href="ticket_details.php?id=<?php echo $ticket['TID']; ?>" class="btn btn-primary">View</a>
                  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu">
                    <li><a href="#" data-toggle="modal" data-target="#setStatusModal<?php echo $ticket['TID']; ?>">Set Status</a></li>
                    <li><a href="#" data-toggle="modal" data-target="#addCommentModal<?php echo $ticket['TID']; ?>">Add Comment</a></li>
                    <li><a href="admin.php?id=<?php echo $ticket['TID']; ?>&action=delete_ticket" onclick="return confirm('Are you sure you want to delete this ticket?')">Delete</a></li>
                  </ul>
                </div>

                <!-- Set Status Modal -->
                <div class="modal fade" id="setStatusModal<?php echo $ticket['TID']; ?>" tabindex="-1" role="dialog" aria-labelledby="setStatusModalLabel<?php echo $ticket['TID']; ?>">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <form method="post" action="admin.php?id=<?php echo $ticket['TID']; ?>&action=status">
                        <div class="modal-header">
                          <h4 class="modal-title" id="setStatusModalLabel<?php echo $ticket['TID']; ?>">Set Status</h4>
                        </div>
                        <div class="modal-body">
                          <div class="form-group">
                            <label for="status">Status:</label>
                            <select class="form-control" id="status" name="status">
                              <option value="new" <?php echo $ticket['STATUS'] === 'new' ? 'selected' : ''; ?>>New</option>
                              <option value="open" <?php echo $ticket['STATUS'] === 'open' ? 'selected' : ''; ?>>Open</option>
                              <option value="postponed" <?php echo $ticket['STATUS'] === 'postponed' ? 'selected' : ''; ?>>Postponed</option>
                              <option value="resolved" <?php echo $ticket['STATUS'] === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                              <option value="answered" <?php echo $ticket['STATUS'] === 'answered' ? 'selected' : ''; ?>>Answered</option>
                            </select>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                          <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
                
                <!-- Add Comment Modal -->
                <div class="modal fade" id="addCommentModal<?php echo $ticket['TID']; ?>" tabindex="-1" role="dialog" aria-labelledby="addCommentModalLabel<?php echo $ticket['TID']; ?>">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <form method="post" action="admin.php?id=<?php echo $ticket['TID']; ?>&action=comment">
                        <div class="modal-header">
                          <h4 class="modal-title" id="addCommentModalLabel<?php echo $ticket['TID']; ?>">Add Comment</h4>
                        </div>
                        <div class="modal-body">
                          <div class="form-group">
                            <label for="comment">Comment:</label>
                            <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                          <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

<!-- User management starts here -->
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h2>User Management</h2>
        <table class="table">
          <thead>
            <tr>
              <th>User ID</th>
              <th>Email</th>
              <th>Admin Flag</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
              // Fetch all user data and store in an array
              $query = "SELECT * FROM ACCOUNT";
              $stmt = $db->prepare($query);
              $stmt->execute();
              $users = $stmt->fetchAll();
            ?>
            <?php foreach ($users as $user): ?>
              <tr>
                <td><?php echo $user['USERID']; ?></td>
                <td><?php echo $user['EMAIL']; ?></td>
                <td><?php echo $user['ADMIN_FLAG']; ?></td>
                <td>
                  <div class="btn-group">
                    <a href="#" data-toggle="modal" data-target="#updateEmailModal<?php echo $user['USERID']; ?>" class="btn btn-primary">Update Email</a>
                     <a href="#" data-toggle="modal" data-target="#updateAdminFlagModal<?php echo $user['USERID']; ?>" class="btn btn-primary">Update Admin Flag</a><a href="admin.php?id=<?php echo $user['USERID']; ?>&action=delete_user" onclick="return confirm('Are you sure you want to delete this user account?')" class="btn btn-danger">Delete</a>
                  </div>
                   <!-- Update Admin Flag Modal -->
              <div class="modal fade" id="updateAdminFlagModal<?php echo $user['USERID']; ?>" tabindex="-1" role="dialog" aria-labelledby="updateAdminFlagModalLabel<?php echo $user['USERID']; ?>">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <form method="post" action="admin.php?id=<?php echo $user['USERID']; ?>&action=update_user">
<div class="modal-header">
<h4 class="modal-title" id="updateAdminFlagModalLabel<?php echo $user['USERID']; ?>">Update Admin Flag</h4>
</div>
<div class="modal-body">
<div class="form-group">
<label for="admin_flag">Admin Flag:</label>
<select class="form-control" id="admin_flag" name="admin_flag" required>
<option value="0" <?php if ($user['ADMIN_FLAG'] == 0) { echo 'selected'; } ?>>No</option>
<option value="1" <?php if ($user['ADMIN_FLAG'] == 1) { echo 'selected'; } ?>>Yes</option>
</select>
</div>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
<button type="submit" class="btn btn-primary">Save changes</button>
</div>
</form>
</div>
</div>
</div>
                  <!-- Update Email Modal -->
                  <div class="modal fade" id="updateEmailModal<?php echo $user['USERID']; ?>" tabindex="-1" role="dialog" aria-labelledby="updateEmailModalLabel<?php echo $user['USERID']; ?>">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">
                        <form method="post" action="admin.php?id=<?php echo $user['USERID']; ?>&action=update_email">
                          <div class="modal-header">
                            <h4 class="modal-title" id="updateEmailModalLabel<?php echo $user['USERID']; ?>">Update Email</h4>
                          </div>
                          <div class="modal-body">
                            <div class="form-group">
                              <label for="email">Email:</label>
                              <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['EMAIL']; ?>" required>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <!-- User management ends here -->

 <fieldset>
<form id="logout" method="POST" action="admin-process.php?action=<?php echo ($action = 'logout'); ?>">
    <input type="submit" value="Logout"/>
  <input type="hidden" name="nonce" value="<?php echo csrf_getNonce('logout'); ?>"/>
</form>
</fieldset>




  <!-- jQuery and Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>