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
// Get the status filter from the AJAX request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $status = $_POST["status"];
}
$userid = $_SESSION['userid'];
// Build the query based on the status filter
if ($status == 'postponed') {
  $query = "SELECT * FROM tickets WHERE status='Postponed'AND userid=$userid";
} else if ($status == 'new') {
  $query = "SELECT * FROM tickets WHERE status='New'AND userid=$userid";
} else if ($status == 'answered') {
  $query = "SELECT * FROM tickets WHERE status='Answered'AND userid=$userid";
  
} else if ($status == 'open') {
    $query = "SELECT * FROM tickets WHERE status='Open'AND userid=$userid";
    
  }else if ($status == 'resolved') {
    $query = "SELECT * FROM tickets WHERE status='Resolved AND userid=$userid'";
    
  }else {
  $query = "SELECT * FROM tickets where USERID=$userid";
}

// Execute the query and fetch the results
//$tickets = $db->query("SELECT * FROM tickets where USERID=$userid");
//echo $query;
$tickets = $db->query($query);
$tickets->fetch(PDO::FETCH_ASSOC);
$results = array();
while ($row = $tickets->fetch(PDO::FETCH_ASSOC)) {
  $results[] = array(
    'id' => $row['TID'],
    'subject' => $row['SUBJECT'],
    'status' => $row['STATUS']  );
  //echo json_encode($results);
}

// // Return the results as JSON
echo json_encode($results);
?>